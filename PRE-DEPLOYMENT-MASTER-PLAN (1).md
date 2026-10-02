# PRE-DEPLOYMENT MASTER PLAN
## School Discipline & Student Monitoring SaaS
### Audit, Full UAT, Responsive/Mobile Overhaul & Production Readiness

**Document Type:** AI Agent Execution Guardrail  
**Status:** ACTIVE  
**Purpose:** Menjadi acuan utama AI Agent sebelum sistem dideploy ke hosting.

---

# 1. TUJUAN UTAMA

Sistem saat ini sudah memiliki rangkaian fitur dari authentication, academic/student management, violation, PKS operations, field activity, hingga reporting.

Sebelum deployment ke hosting, AI Agent WAJIB memastikan:

1. Seluruh fitur existing tetap berfungsi.
2. Seluruh workflow end-to-end dapat digunakan oleh user nyata.
3. Authorization dan multi-tenant isolation aman.
4. Daily/Monthly Report menghasilkan data yang konsisten.
5. PDF dan Excel berfungsi.
6. UI responsive, terutama pada handphone.
7. Tidak ada regression setelah perubahan UI.
8. Production configuration siap.
9. Semua automated test tetap PASS.
10. Tidak boleh mengurangi atau menghapus fitur existing hanya demi memperbaiki UI.

**PRINSIP UTAMA:**
> Jangan mengejar "tests pass" saja. Sistem harus benar-benar usable dari sudut pandang user.

---

# 2. KONDISI TEKNOLOGI

Gunakan teknologi existing. Jangan mengganti stack tanpa alasan teknis yang kuat.

- Laravel 13.31.0
- PHP 8.3.22
- Bootstrap 5.3.8
- Laravel Breeze Blade
- Alpine.js
- Vite 8
- SQLite untuk development saat ini
- Modular Monolith
- Multi-tenancy berbasis `school_id`
- TenantContext / TenantContextMiddleware
- BelongsToTenant
- Role:
  - super_admin
  - school_admin
  - operator
  - teacher

Reporting:
- barryvdh/laravel-dompdf
- maatwebsite/excel

Current automated test baseline:
- 377 tests
- 805 assertions

Baseline ini WAJIB dipertahankan atau bertambah. Jangan menghapus test existing untuk membuat suite PASS.

---

# 3. WORKING PRINCIPLE

AI Agent WAJIB bekerja dengan urutan:

```text
AUDIT
  ↓
DOCUMENT FINDINGS
  ↓
PLAN
  ↓
IMPLEMENT/FIX
  ↓
TEST
  ↓
VERIFY
  ↓
REPORT
```

Jangan langsung mengubah banyak file tanpa memahami architecture existing.

Sebelum melakukan perubahan besar:

- baca route
- baca controller
- baca model
- baca policy
- baca request validation
- baca migration
- baca view
- baca test terkait

---

# 4. ATURAN KERAS

## 4.1 Jangan Menghapus Fitur Existing

Tidak boleh:

- menghapus module
- menghapus route
- menghapus CRUD
- menghapus permission
- menghapus tenant isolation
- menghapus report
- menghapus export
- menghapus validation
- menghapus test hanya karena test gagal

## 4.2 Jangan Mengganti Architecture Tanpa Kebutuhan

Jangan melakukan:

- migrasi framework
- mengganti Bootstrap ke Tailwind
- mengganti Breeze
- mengganti database
- mengganti tenant architecture
- mengganti reporting package

kecuali benar-benar diperlukan dan disetujui.

## 4.3 Jangan Menganggap Test Pass = Feature Berfungsi

Automated test harus dikombinasikan dengan:

- browser verification
- workflow testing
- role testing
- responsive testing
- data consistency testing

## 4.4 Jangan Menyalahkan Browser Tanpa Bukti

Jika UI tidak menampilkan action Create/Edit/Delete, jangan menyimpulkan cache/browser.

Periksa:

- route
- controller
- authorization
- Blade view
- button/action
- conditional rendering
- CSS/JS
- browser console

## 4.5 Jangan Mengurangi Functionality Demi Responsive

Responsive fix harus mempertahankan functionality.

---

# 5. CURRENT PRODUCT WORKFLOW

Core operational workflow:

```text
PKS Member
    ↓
Duty Schedule
    ↓
Duty Location
    ↓
Duty Assignment
    ↓
Duty Attendance
    ↓
Field Activity
    ↓
Optional Violation
    ↓
Daily Report
    ↓
Monthly Report
    ↓
PDF / Excel
```

Academic/violation dependency:

```text
Academic Year
    ↓
Department
    ↓
Class
    ↓
Student
    ↓
Violation Type
    ↓
Violation
```

---

# 6. MODULE AUDIT

Audit seluruh module berikut.

## Authentication

- Login
- Logout
- Guest protection
- Session
- Role authorization

## Academic

- Academic Year
- Department
- Class
- Student

Audit:
- list
- create
- detail
- edit
- delete/cancel jika tersedia
- search
- filter
- pagination
- validation

## Violation

- Violation Type
- Violation
- Evidence
- Point
- Status
- Cancellation

Audit:
- create
- edit
- detail
- validation
- point snapshot
- evidence access
- authorization
- tenant isolation

## PKS

- PKS Member
- PKS Shift
- Duty Location
- Duty Schedule
- Duty Assignment
- Duty Attendance
- Field Activity

Audit seluruh CRUD/workflow dan permission.

## Reporting

- Daily Report
- Daily Filter
- Daily PDF
- Monthly Report
- Monthly Filter
- Monthly Excel

Pastikan data report konsisten dengan source data.

---

# 7. ROLE TESTING

## Super Admin

Audit kemampuan platform-level dan akses tenant.

## School Admin

Harus dapat melakukan administrasi sekolah yang memang diizinkan.

Minimal workflow:

```text
Login
→ Academic setup
→ Student
→ Violation Type
→ PKS Member
→ Shift
→ Location
→ Schedule
→ Assignment
→ Monitoring
→ Report
→ Export
```

## Operator

Fokus pada operational workflow:

```text
Login
→ View Schedule
→ Attendance
→ Field Activity
→ Violation
→ Report sesuai permission
```

## Teacher

Pastikan hanya dapat melakukan action yang memang diizinkan.

Setiap role harus diuji:

- allowed action
- forbidden action
- direct URL
- route manipulation
- unauthorized POST/PUT/DELETE jika relevan

---

# 8. MULTI-TENANT SECURITY

Minimal gunakan dua tenant:

```text
SMK 1 Jakarta
SMK 2 Surabaya
```

Test:

```text
Tenant A login
→ hanya melihat Tenant A

Tenant B login
→ hanya melihat Tenant B
```

Jangan hanya test UI.

Test juga:

- URL manipulation
- ID manipulation
- school_id manipulation
- direct route
- report query
- evidence access
- related model access

Non-super-admin tidak boleh memilih tenant lain melalui request.

---

# 9. NEGATIVE TESTING

AI Agent WAJIB mencari dan menguji kasus invalid.

Contoh:

- duplicate NIS
- invalid student
- invalid violation type
- inactive PKS member
- duplicate assignment
- location tidak termasuk schedule
- duplicate attendance
- invalid start/end time
- unauthorized action
- cross-tenant ID
- invalid upload
- oversized upload
- empty report
- invalid date/filter

Expected:

- validation error yang jelas
- authorization response yang benar
- tidak ada unexpected 500 error

---

# 10. RESPONSIVE / MOBILE OVERHAUL

## PRIORITAS

Mobile adalah prioritas utama karena PKS/operator berpotensi menggunakan sistem langsung di lapangan.

Target viewport:

- 360 × 800
- 375 × 812
- 390 × 844
- 412 × 915
- 768 × 1024
- 1024 × 768
- 1366 × 768

## Semua view WAJIB diaudit

Minimal:

- Login
- Dashboard
- Academic Year
- Department
- Class
- Student
- Violation Type
- Violation
- PKS Member
- Shift
- Location
- Schedule
- Assignment
- Attendance
- Field Activity
- Daily Report
- Monthly Report

---

# 11. RESPONSIVE ACCEPTANCE CRITERIA

Pada mobile:

- tidak ada page-level horizontal overflow
- sidebar usable
- navbar usable
- button tidak keluar layar
- form tidak overflow
- input nyaman disentuh
- table tetap usable
- modal tidak terpotong
- alert tidak rusak
- pagination usable
- filter usable
- text tidak bertabrakan
- action button accessible

Gunakan Bootstrap responsive utilities yang sudah ada sebelum membuat CSS custom yang berlebihan.

---

# 12. TABLE STRATEGY

Jangan memaksa semua kolom desktop tampil penuh pada mobile.

Gunakan strategi yang sesuai:

- `.table-responsive`
- hide less-important columns
- stacked/card layout jika lebih tepat
- action dropdown jika action terlalu banyak
- detail page untuk informasi lengkap

Jangan menghapus data penting hanya supaya tabel terlihat kecil.

---

# 13. FORM STRATEGY

Desktop:

```text
2-column layout jika sesuai
```

Mobile:

```text
single-column
```

Pastikan:

- label jelas
- input full width
- button mudah disentuh
- validation message terlihat
- required field jelas
- select tidak terpotong

---

# 14. MOBILE-FIRST OPERATIONAL PAGES

Berikan perhatian ekstra pada:

## Attendance

Flow harus praktis:

```text
Schedule
→ Assignment
→ Attendance
→ Check in/out
→ Save
```

## Field Activity

Flow:

```text
Assignment
→ Activity Type
→ Start
→ Finding
→ Action Taken
→ Status
→ Save
```

## Violation

Flow:

```text
Student
→ Violation Type
→ Location
→ Description
→ Evidence jika diperlukan
→ Save
```

User lapangan tidak boleh dipaksa menggunakan desktop-style interface.

---

# 15. REPORTING VERIFICATION

Daily Report harus diverifikasi terhadap source data.

Contoh:

```text
Assignment = 4
Attendance = 4
Activity = 2
Violation = 1
```

Angka di report harus bisa ditelusuri ke data sebenarnya.

Test:

- date filter
- shift filter
- location filter
- status filter
- empty state
- PDF

Monthly:

- month/year
- shift
- location
- daily breakdown
- summary
- Excel

Excel wajib memiliki:

1. Ringkasan
2. Jadwal
3. Penugasan
4. Kehadiran
5. Aktivitas
6. Pelanggaran

Pastikan summary dan detail konsisten.

---

# 16. FULL END-TO-END UAT

Lakukan simulasi sekolah nyata.

## H-1

School Admin:

```text
Create schedule
→ Select shift
→ Select locations
→ Assign PKS
```

## Hari H

Operator:

```text
Open today's schedule
→ Record attendance
→ Perform monitoring
→ Record field activity
→ Record violation jika terjadi
→ Complete activity
```

## Setelah kegiatan

School Admin:

```text
Open Daily Report
→ Verify data
→ Export PDF
```

## Akhir bulan

School Admin:

```text
Open Monthly Report
→ Review summary
→ Review daily breakdown
→ Export Excel
```

Workflow harus dapat dilakukan tanpa database editing manual.

---

# 17. REGRESSION TESTING

Setelah setiap perubahan UI/responsive:

```bash
php artisan test
npm run build
```

Jika ada test failure:

1. identifikasi root cause
2. tentukan apakah regression atau test memang outdated
3. perbaiki implementation
4. jangan menghapus test hanya agar PASS
5. jalankan ulang seluruh suite

Target:

```text
0 failed
```

Test count boleh bertambah.

---

# 18. CODE QUALITY AUDIT

Cari:

- dead code
- debug statements
- `dd()`
- `dump()`
- temporary routes
- temporary credentials
- hardcoded tenant IDs
- hardcoded production URLs
- insecure file paths
- unused imports
- obvious N+1 queries pada report
- missing authorization
- unsafe mass assignment
- missing validation

Jangan melakukan refactor besar jika tidak diperlukan untuk deployment.

---

# 19. PRODUCTION READINESS

Sebelum deployment, verify:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=...
```

Audit:

- database config
- storage
- cache
- session
- queue jika digunakan
- filesystem
- mail jika digunakan
- public storage link
- upload directory
- permissions
- migrations
- seeders
- build assets

Jangan commit production secret ke repository.

---

# 20. DATABASE COMPATIBILITY

Development saat ini menggunakan SQLite.

Sebelum production MySQL/MariaDB:

Audit:

- migrations
- enum usage
- date/time handling
- foreign keys
- indexes
- unique constraints
- JSON fields jika ada
- query compatibility
- SQLite-specific behavior

Jika production DB berbeda, lakukan compatibility verification sebelum deployment.

---

# 21. FINAL SMOKE TEST

Setelah production build:

```bash
php artisan test
npm run build
```

Kemudian browser smoke test:

```text
Login
→ Dashboard
→ Student
→ PKS
→ Schedule
→ Assignment
→ Attendance
→ Activity
→ Violation
→ Daily Report
→ PDF
→ Monthly Report
→ Excel
→ Logout
```

---

# 22. BUG CLASSIFICATION

Setiap temuan wajib dikategorikan.

### P0 — BLOCKER

Contoh:

- login gagal
- data tenant bocor
- database corrupt
- critical action bypass authorization

Deployment STOP.

### P1 — CRITICAL

Contoh:

- core CRUD tidak bekerja
- report menghasilkan data salah
- attendance gagal
- violation gagal
- PDF/Excel rusak

Deployment STOP.

### P2 — MAJOR

Contoh:

- mobile workflow sulit digunakan
- filter tertentu rusak
- UI broken pada viewport tertentu

Harus diperbaiki sebelum deployment jika memengaruhi penggunaan utama.

### P3 — MINOR

Contoh:

- spacing
- wording
- cosmetic issue

Dapat dicatat sebagai backlog jika tidak mengganggu usability/security.

---

# 23. DEFINITION OF DONE

Task ini hanya dianggap selesai jika:

- [ ] Semua module telah diaudit
- [ ] Semua critical CRUD verified
- [ ] Role authorization verified
- [ ] Tenant isolation verified
- [ ] Negative testing selesai
- [ ] Daily report verified
- [ ] Monthly report verified
- [ ] PDF verified
- [ ] Excel verified
- [ ] Mobile 360px verified
- [ ] Mobile 390px verified
- [ ] Tablet verified
- [ ] Desktop verified
- [ ] No page-level horizontal overflow
- [ ] No critical console errors
- [ ] No debug code
- [ ] Full automated test PASS
- [ ] Production build PASS
- [ ] Final smoke test PASS

---

# 24. JANGAN MELAKUKAN DEPLOYMENT OTOMATIS

AI Agent TIDAK BOLEH:

- deploy ke hosting
- mengubah production database
- menghapus production data
- mengubah DNS
- mengubah production credentials

kecuali ada instruksi eksplisit dari user.

Tugas agent pada fase ini adalah:

> Audit → Fix → Test → Verify → Report.

---

# 25. FINAL REPORT YANG WAJIB DIBERIKAN AGENT

Setelah selesai, buat report dengan format:

## A. Audit Summary

Jumlah module diperiksa.

## B. Issues Found

| ID | Module | Severity | Problem | Fix |
|---|---|---|---|---|

## C. Responsive Audit

| View | 360 | 390 | 768 | Desktop | Status |
|---|---|---|---|---|---|

## D. Security

- Authorization:
- Tenant isolation:
- Direct URL:
- Upload security:

## E. Automated Tests

```text
Before:
377 tests / 805 assertions

After:
[actual result]
```

## F. Build

```text
npm run build:
PASS/FAIL
```

## G. Manual UAT

```text
Authentication:
PASS/FAIL

Academic:
PASS/FAIL

PKS:
PASS/FAIL

Attendance:
PASS/FAIL

Activity:
PASS/FAIL

Violation:
PASS/FAIL

Reporting:
PASS/FAIL

PDF:
PASS/FAIL

Excel:
PASS/FAIL

Responsive:
PASS/FAIL
```

## H. Remaining Issues

List all unresolved issues honestly.

## I. Deployment Recommendation

Gunakan hanya:

```text
READY FOR DEPLOYMENT
```

atau:

```text
NOT READY FOR DEPLOYMENT
```

Jangan menyatakan READY jika masih ada P0/P1 atau critical workflow yang belum diverifikasi.

---

# 26. FINAL INSTRUCTION TO AI AGENT

> Ambil dokumen ini sebagai guardrail utama untuk pekerjaan Pre-Deployment.
>
> Jangan langsung coding.
>
> Mulai dengan audit kondisi repository dan cocokkan implementasi aktual dengan dokumen ini.
>
> Setelah audit, identifikasi gap.
>
> Prioritaskan:
>
> 1. Functional correctness
> 2. End-to-end workflow
> 3. Authorization
> 4. Tenant isolation
> 5. Mobile/responsive usability
> 6. Reporting accuracy
> 7. Production readiness
>
> Jangan menghapus fitur existing.
>
> Jangan mengurangi functionality demi responsive.
>
> Jangan menganggap automated tests sebagai satu-satunya bukti feature bekerja.
>
> Setelah melakukan perubahan, jalankan full regression test dan build.
>
> Semua keputusan perubahan harus mempertahankan architecture existing kecuali ada alasan teknis yang jelas.
>
> Pada akhir pekerjaan, berikan Final Report sesuai format dokumen ini dan nyatakan secara jujur apakah sistem READY atau NOT READY untuk deployment.

---

# 27. PRIORITY ORDER

Jika menemukan banyak masalah, gunakan urutan:

```text
P0 Security / Data Loss
        ↓
P1 Core Functionality
        ↓
Tenant Isolation
        ↓
Authorization
        ↓
Data Integrity
        ↓
Reporting Accuracy
        ↓
Mobile Usability
        ↓
Desktop UX
        ↓
Cosmetic Polish
```

**Jangan menghabiskan waktu mempercantik UI jika ada bug authorization atau data isolation.**

---

# END OF DOCUMENT
