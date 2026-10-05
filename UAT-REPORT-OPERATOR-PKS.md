# UAT END-TO-END — PETUGAS PKS / OPERATOR

**Status:** AUDIT SELESAI + PERBAIKAN F1 & F2 DITERAPKAN (lihat §L)
**Tanggal:** 2026-10-05
**Actor utama:** `operator` (email `operator@smk1jkt.sch.id`, school_id=1, SMK 1 Jakarta)
**Metode:** Audit kode (routes, controllers, policies, FormRequest, model, migration, view) + HTTP-level E2E terhadap dev server (`127.0.0.1:8000`) + automated regression test.
**Batas metode:** Tidak ada browser automation yang tersedia, sehingga uji visual/mobile tidak dilakukan secara nyata — hanya audit kode responsive + verifikasi HTTP.

---

## A. Environment

| Item | Nilai |
|---|---|
| Framework | Laravel 13.31.0 |
| PHP | 8.3.22 |
| Frontend | Bootstrap 5.3.8, Bootstrap Icons, Alpine.js |
| Build | Vite 8 (`npm run build`) |
| Database | SQLite (development) |
| Multi-tenancy | `school_id` + `BelongsToTenant` global scope |
| Roles | super_admin, school_admin, operator, teacher |
| Reporting | barryvdh/laravel-dompdf (PDF), maatwebsite/excel (Excel) |
| Dev server | Running at `http://127.0.0.1:8000` |

---

## B. Scope Operator (sesuai role definition)

Berdasarkan `PRE-DEPLOYMENT-MASTER-PLAN (1).md` §7 dan `SYSTEM-FLOW-UAT-MASTER-GUIDE.md` §2, role **Operator** hanya untuk:

```text
Login → View Schedule → Attendance → Field Activity → Violation → Report (sesuai permission)
```

Setup academic/student, violation master, PKS master, shift, location, schedule, dan assignment adalah tanggung jawab **School Admin** (H-1), bukan Operator.

---

## C. Authorization Matrix — Operator (hasil HTTP)

| Aksi | Endpoint | HTTP | Status | Keterangan |
|---|---|---|---|---|
| Login | POST /login | 302 → /dashboard | ✅ PASS | |
| Dashboard | GET /dashboard | 200 | ✅ PASS | |
| View jadwal (list) | GET /pks-duty-schedules | 200 | ✅ PASS | |
| View jadwal (detail) | GET /pks-duty-schedules/1 | 200 | ✅ PASS | |
| View penugasan | GET /pks-duty-assignments | 200 | ✅ PASS | |
| View kehadiran | GET /pks-duty-attendances | 200 | ✅ PASS | |
| View aktivitas | GET /pks-field-activities | 200 | ✅ PASS | |
| View pelanggaran | GET /violations | 200 | ✅ PASS | |
| Report harian | GET /reports/daily | 200 | ✅ PASS | |
| Report PDF | GET /reports/daily/pdf | 200 (`application/pdf`) | ✅ PASS | |
| Report bulanan | GET /reports/monthly?month=9&year=2026 | 200 | ✅ PASS | |
| Report Excel | GET /reports/monthly/excel?month=9&year=2026 | 200 (xlsx) | ✅ PASS | |
| Buat pelanggaran | POST /violations | 302 (sukses) | ✅ PASS | Core operator task |
| Buat jadwal | GET /pks-duty-schedules/create | 403 | ✅ PASS | Diblokir (admin-only) |
| Buat penugasan | GET /pks-duty-assignments/create | 403 | ✅ PASS | Diblokir (admin-only) |
| Buat shift | GET /pks-shifts/create | 403 | ✅ PASS | Diblokir |
| Buat lokasi | GET /pks-duty-locations/create | 403 | ✅ PASS | Diblokir |
| Buat tahun ajaran | GET /academic-years/create | 403 | ✅ PASS | Diblokir |
| Buat program keahlian | GET /departments/create | 403 | ✅ PASS | Diblokir |
| Buat kelas | GET /classes/create | 403 | ✅ PASS | Diblokir |
| Buat jenis pelanggaran | POST /violation-types | 403 | ⚠️ Server OK | Form-nya bocor (lihat F2) |
| Buat anggota PKS | POST /pks-members | 403 | ⚠️ Server OK | Form-nya bocor (lihat F2) |
| Buat siswa | POST /students | 302 (sukses) | ❌ GAP | Lihat F1 |

---

## D. Tenant Isolation (hasil HTTP)

| Uji | Hasil | Status |
|---|---|---|
| Operator school 1 → GET /students/16 (siswa school 2) | 404 | ✅ PASS |
| Report selalu di-scope `school_id` user (non-super-admin) | dikonfirmasi di `PksReportController::resolveSchoolId` | ✅ PASS |
| `BelongsToTenant` global scope pada Student/Violation/PksMember/AcademicYear/Department/SchoolClass | dikonfirmasi | ✅ PASS |

Tidak ditemukan tenant leak. Cross-tenant ID manipulation mengembalikan 404 (bukan 200/data bocor).

---

## E. Findings

| ID | Severity | Kategori | Problem | Evidence | Fix |
|---|---|---|---|---|---|
| F1 | **P2** | Authorization | **Operator dapat membuat/mengedit/menghapus siswa.** `StoreStudentRequest` & `UpdateStudentRequest` mengizinkan `operator`; `StudentController::create()` mengizinkan operator; `edit()` & `destroy()` **tidak punya authorization check sama sekali**. | HTTP POST /students sebagai operator → 302 sukses. | Batasi create/update/destroy siswa ke super_admin + school_admin; tambahkan `authorize()` di `edit()`/`destroy()`/`update()`. |
| F2 | **P3** | UX | Operator melihat form "Tambah" untuk **Jenis Pelanggaran** dan **Anggota PKS** (GET create = 200), padahal POST ditolak 403. | GET /violation-types/create=200, POST=403; GET /pks-members/create=200, POST=403. | Sembunyikan tombol "Tambah" untuk non-admin, dan/atau tambahkan `$this->authorize('create', ...)` di `create()` agar form 403. |
| F3 | **P3** | Data setup | Data fixture UAT tidak cocok dengan seed DB, sehingga alur Hari-H operator (attendance → activity) **terblokir di level data**. | Shift="PAGI" 06:00–07:00 (bukan "Piket Pagi" 06:30–08:00); schedule=2026-09-22 (bukan 09-23); lokasi hanya "Gerbang Depan"; satu-satunya PKS member (id 1) INACTIVE & terhubung ke "Fajar Nugroho" (bukan Ahmad); violation type "Tidak Helm"=5 poin (bukan "Tidak menggunakan helm" 10 poin); tidak ada siswa "Candra"/"Deni". | Bukan code bug — setup data H-1 oleh School Admin (aktifkan member, buat 4 lokasi, schedule 23 Sep, assignment). |
| F4 | UNCLEAR | Requirement | Operator **tidak dapat verify/cancel** pelanggaran (`ViolationPolicy::verify/cancel` mensyaratkan school_admin). | Audit `ViolationPolicy.php`. | Konfirmasi ke owner: verifikasi pelanggaran apakah memang hak admin (master plan §7 operator tidak menyebut "verify"). Jika operator wajib verify → tambah izin. |
| F5 | **P3** | Authorization | Teacher dapat membuat pelanggaran (`StoreViolationRequest` & `ViolationPolicy::create` menyertakan teacher), padahal role teacher = "monitoring/view". | Audit `StoreViolationRequest.php` & `ViolationPolicy.php`. | Konfirmasi scope teacher; jika hanya view, hapus teacher dari create. |
| F6 | P3 | Data integrity | Tidak ada pencegahan duplikat pelanggaran di level DB/validation (tabel `violations` tanpa unique constraint; tidak ada cek duplicate di `StoreViolationRequest`). | Audit migration & request. | (Opsional) tambah unique atau validasi duplicate untuk mencegah pencatatan ganda. |

---

## F. Negative Testing

Dilakukan dua lapis: **automated test suite** (sudah mencakup negative cases) dan **verifikasi HTTP**:

| Kasus negatif | Hasil | Sumber |
|---|---|---|
| Duplicate attendance | ditolak (unique + `validateNoExistingAttendance`) | automated PASS |
| Duplicate assignment | ditolak (unique) | automated PASS |
| Duplicate NIS | ditolak (unique per school) | automated PASS |
| Invalid student / violation type (cross-school) | ditolak (`Rule::exists` + school scope) | automated PASS |
| Inactive PKS member | ditolak saat assignment | automated PASS |
| Location bukan bagian schedule | ditolak | automated PASS |
| `ended_at < started_at` | ditolak (`after_or_equal`) | automated PASS |
| Cross-tenant ID (student 16 dari school 2) | 404 | HTTP PASS |
| Operator POST violation-type / pks-member | 403 | HTTP PASS |
| Operator POST student (seharusnya ditolak) | **302 sukses** ❌ | HTTP — lihat F1 |

Tidak ada `500 unexpected` yang teramati pada uji HTTP.

---

## G. Mobile / Responsive

**Tidak diverifikasi secara visual (tidak ada browser automation).** Dilakukan audit kode:

- CSS memiliki breakpoint `@media (max-width: 768px)` dan `(max-width: 640px)`.
- Sidebar → overlay + `translateX(-100%)` di mobile, toggle via hamburger.
- Tabel dibungkus `.table-responsive` (horizontal scroll) — dapat diterima, belum ada stacked/card layout.
- `page-header`, `stat-card`, `welcome-banner`, `quick-actions` sudah punya rule mobile.

**Status: NOT VERIFIED (perlu browser/device test 360/390/412 px).**

| View | 360 | 390 | 412 | Desktop | Status |
|---|---|---|---|---|---|
| Login | — | — | — | — | not verified |
| Dashboard | — | — | — | 200 | not verified (mobile) |
| Schedule / Assignment / Attendance / Activity / Violation | — | — | — | 200 | not verified (mobile) |
| Daily/Monthly Report | — | — | — | 200 | not verified (mobile) |

---

## H. Automated Regression

```text
php artisan test
Tests:      380
Assertions: 809
Failed:     0
Duration:   ~21s
Exit code:  0
```

Suite mencakup: `PksDutyAttendanceTest` (49), `PksFieldActivityTest`, `PksDutyAssignmentTest`, `PksDutyScheduleTest`, `PksReportTest`, `ViolationTest`, `ViolationTypeTest`, `TenantIsolationTest`, `AuthorizationTest`, `StudentTest`, dll. **Seluruhnya PASS** — baseline 377/805 dipertahankan dan bertambah 3 test (coverage authorization baru untuk F1/F2).

```text
npm run build
Result: PASS (built in ~15.6s)
CSS:  public/build/assets/app-BKvRgby5.css (satu file — konfirmasi fix double-Bootstrap)
JS:   public/build/assets/app-BXs5bz91.js
```

---

## I. Manual UAT Summary

```text
Authentication:        PASS
View schedule:         PASS
View assignment:       PASS
Attendance:            PASS (automated) — belum manual E2E (data blocker F3)
Field activity:        PASS (automated) — belum manual E2E (data blocker F3)
Violation create:      PASS (HTTP)
Report daily:          PASS (HTTP)
PDF:                   PASS (HTTP)
Report monthly:        PASS (HTTP)
Excel:                 PASS (HTTP)
Authorization:         FIXED (F1, F2) — sisa F4 (UNCLEAR) & F5 (P3)
Tenant isolation:      PASS
Responsive/mobile:     NOT VERIFIED
```

---

## J. Remaining Issues

1. ~~F1 (P2) — Operator bisa mengelola siswa~~ ✅ **FIXED** (lihat §L).
2. ~~F2 (P3) — Form "Tambah" jenis pelanggaran & anggota PKS bocor ke operator~~ ✅ **FIXED** (lihat §L).
3. **F3 (P3)** — Data seed belum menyiapkan alur H-1 (member aktif, 4 lokasi, schedule 23 Sep, assignment) sehingga simulasi Hari-H operator belum bisa dijalankan manual end-to-end.
4. **F4 (UNCLEAR)** — Perlu konfirmasi apakah operator berhak verify/cancel pelanggaran.
5. **F5 (P3)** — Perlu konfirmasi scope teacher (apakah boleh buat pelanggaran).
6. **F6 (P3)** — Tidak ada pencegahan duplikat pelanggaran di level DB/validation.
7. **Mobile 360/390/412 px belum diverifikasi visual.**

---

## K. Final Decision

```text
NOT READY FOR DEPLOYMENT
```

**Alasan:** gap authorization **P2 (F1)** dan UX **P3 (F2)** sudah diperbaiki dan lolos regression. Namun belum layak deploy karena: **mobile/responsive belum diverifikasi secara nyata** (prioritas untuk sistem lapangan PKS), dan **alur Hari-H operator belum selesai diuji manual end-to-end** (masih terblokir data setup F3). Tidak ada P0/P1 tersisa.

**Rekomendasi langkah berikut (berurutan):**
1. Setup data H-1 (aktifkan PKS member, buat lokasi/shift sesuai skenario, schedule 23 Sep, assignment) lalu jalankan ulang simulasi Hari-H operator.
2. Verifikasi mobile 360/390/412 px (device/browser).
3. Konfirmasi F4 & F5 ke product owner; opsional implement F6 (pencegahan duplikat pelanggaran).
4. Re-run `php artisan test` + `npm run build` (sudah PASS: 380 tests / 0 failed).

Setelah mobile terverifikasi dan alur Hari-H operator lolos end-to-end, sistem dapat dinilai ulang menuju `READY FOR DEPLOYMENT`.

---

## L. Perbaikan yang Diterapkan (Fix Applied)

### L.1 — F1 (P2): Operator tidak lagi bisa mengelola siswa
| File | Perubahan |
|---|---|
| `app/Http/Requests/StoreStudentRequest.php` | `authorize()`: hapus `isOperator()` → hanya super_admin + school_admin. |
| `app/Http/Requests/UpdateStudentRequest.php` | `authorize()`: hapus `isOperator()` → hanya super_admin + school_admin. |
| `app/Http/Controllers/StudentController.php` | `create()`: hapus `isOperator()` dari `abort_unless`. `edit()` & `destroy()`: tambah `abort_unless(super/admin, 403)`. |
| `tests/Feature/StudentTest.php` | `test_operator_can_create_and_edit_students` → `test_operator_cannot_create_or_edit_students` (403). `test_can_update_student` → `test_school_admin_can_update_student` (actor admin). |

**Verifikasi HTTP (operator):** GET /students/create → 403, POST /students → 403.

### L.2 — F2 (P3): Form create/edit jenis pelanggaran & anggota PKS tidak lagi bocor
| File | Perubahan |
|---|---|
| `app/Http/Controllers/ViolationTypeController.php` | `create()` → `authorize('create')`; `edit()` → `authorize('update')`; `destroy()` → `authorize('delete')`. |
| `app/Http/Controllers/PksMemberController.php` | `create()` → `authorize('create')`; `edit()` → `authorize('update')`. |
| `app/Policies/ViolationTypePolicy.php` | `delete()`: hapus cek `isUsed()` yang redundant (friendly-redirect "sudah dipakai" kini ditangani controller, bukan 403). |
| `tests/Feature/ViolationTypeTest.php` | + `test_operator_cannot_access_create_and_edit_forms`, + `test_operator_cannot_delete_violation_type`. |
| `tests/Feature/PksMemberTest.php` | + `test_operator_cannot_access_create_and_edit_forms`. |

**Verifikasi HTTP (operator):** GET /violation-types/create → 403, /pks-members/create → 403, /violation-types/3/edit → 403. Core operator action (violations/attendances/field-activities create) tetap 200.

**Catatan:** tidak ada perubahan business flow, validation, policy role lain, database, atau route. Hanya menutup authorization gap + form visibility. F3–F6 sengaja tidak diubah (perlu konfirmasi/setup data, bukan bug kode).
