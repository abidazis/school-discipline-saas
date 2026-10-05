# SYSTEM FLOW UAT MASTER GUIDE
## School Discipline & Student Monitoring SaaS

**Status:** ACTIVE — PRE-DEPLOYMENT  
**Purpose:** Guardrail AI Agent dan acuan manual tester agar seluruh implementasi dan pengujian mengikuti alur penggunaan sistem yang telah dirancang.

## 1. Prinsip Utama

Jangan menguji aplikasi hanya sebagai kumpulan halaman. Uji sebagai **workflow operasional sekolah end-to-end**.

Alur utama:

```text
Setup Sekolah
→ Academic Year
→ Department
→ Class
→ Student
→ Violation Type
→ PKS Member
→ Shift
→ Location
→ Duty Schedule
→ Duty Assignment
→ Duty Attendance
→ Field Activity
→ Violation
→ Daily Report
→ PDF
→ Monthly Report
→ Excel
```

Target akhir: user sekolah dapat menyelesaikan pekerjaan nyata tanpa database editing manual.

## 2. Role

### Super Admin
Platform/tenant management dan akses lintas tenant sesuai permission. Bukan operator PKS harian.

### School Admin
Setup sekolah, academic/student, violation master, PKS master, shift, location, schedule, assignment, monitoring, reporting.

### Operator
Operasional harian: schedule, attendance, field activity, pencatatan kejadian/pelanggaran, report sesuai permission.

### Teacher
Monitoring/view access sesuai permission; tidak boleh memperoleh action administrasi yang tidak diizinkan.

## 3. Skenario UAT

Gunakan data konsisten:

```text
Tenant A: SMK 1 Jakarta
Tenant B: SMK 2 Surabaya
Academic Year: 2026/2027

Departments:
TKJ, TKR, AKL

Classes:
X TKJ 1
XI TKJ 1
XII TKJ 1

Students:
Ahmad, Budi, Candra, Deni, Eko

Violation Types:
Tidak menggunakan helm (10 poin)
Datang terlambat
Tidak lengkap atribut
Keluar sekolah tanpa izin

PKS:
Ahmad, Budi, Candra, Deni

Shift:
Piket Pagi — 06:30–08:00

Locations:
Gerbang Depan
Gerbang Belakang
Parkiran
Masjid

Test date:
23 September 2026
```

## 4. Workflow A — Academic & Student Setup

**Actor:** School Admin

```text
Login
→ Academic Year
→ Department
→ Class
→ Student
```

Verifikasi relationship:

```text
2026/2027
→ TKJ
→ X TKJ 1
→ Ahmad
```

Test create, detail, edit, search, filter, pagination, validation, duplicate handling.

## 5. Workflow B — Violation Master

```text
Student tersedia
→ Create Violation Type
→ Violation siap dipakai
```

Verifikasi code, category, severity, points, active status dan validation.

## 6. Workflow C — PKS Master

```text
Student
→ PKS Member
→ Position
→ Active

PKS Shift
+
Duty Location
```

Pastikan hanya data yang valid/aktif dapat dipakai dalam operational flow sesuai aturan sistem.

## 7. Workflow D — Duty Schedule

**Actor:** School Admin

```text
Create Schedule
→ Date
→ Shift
→ Locations
→ Scheduled
```

Contoh 23 September 2026, Piket Pagi, empat lokasi.

Detail schedule harus menampilkan data yang benar.

## 8. Workflow E — Duty Assignment

Contoh:

```text
Ahmad  → Gerbang Depan
Budi   → Gerbang Belakang
Candra → Parkiran
Deni   → Masjid
```

Negative test:

- inactive member
- member tenant lain
- location bukan bagian schedule
- duplicate assignment
- schedule tidak eligible

Semua harus ditolak dengan validation/authorization yang benar.

## 9. Workflow F — Duty Attendance

**Actor:** Operator

```text
Login
→ Today's Schedule
→ Assignment
→ Attendance
```

Contoh:

```text
Ahmad  → Hadir, 06:27–08:02
Budi   → Terlambat, 06:47
Candra → Hadir
Deni   → Izin
```

Pastikan attendance terhubung ke assignment dan summary konsisten.

## 10. Workflow G — Field Activity

**Actor:** Operator

```text
Assignment
→ Activity Type
→ Start
→ Finding
→ Action Taken
→ Status
→ Save
```

Contoh:

```text
Gate Monitoring
Finding: beberapa siswa datang mendekati batas waktu
Action: teguran lisan
Status: Completed
```

Buat juga Parking Monitoring dengan finding dua siswa tidak menggunakan helm.

## 11. Workflow H — Violation Operasional

Simulasikan kejadian:

```text
Parking Monitoring
→ Finding pelanggaran
→ Buat Violation
```

Contoh:

```text
Student: Budi
Violation: Tidak menggunakan helm
Points: 10
Location: sesuai skenario
```

Jika implementasi mendukung Activity → Violation, verifikasi relasinya.

**Jangan mengasumsikan activity otomatis membuat violation jika fitur itu belum benar-benar diimplementasikan.**

## 12. Workflow I — Daily Report

**Actor:** role yang memiliki akses report.

```text
Daily Report
→ 23 September 2026
→ Review
```

Verifikasi:

- schedules
- assignments
- attendance
- activities
- violations

Cross-check:

```text
Source Data == Web Report
```

## 13. Workflow J — Daily Filters

Test:

```text
Date
Shift
Location
Status
```

Test kombinasi filter jika tersedia. Dataset harus berubah sesuai kriteria.

## 14. Workflow K — Daily PDF

```text
Daily Report
→ Export PDF
→ Open PDF
```

Periksa:

- nama sekolah
- tanggal
- summary
- schedule
- assignment
- attendance
- activity
- violation
- signature
- page number

Cross-check:

```text
Web Report == PDF
```

## 15. Workflow L — Monthly Report

```text
Monthly Report
→ September 2026
→ Review Summary
→ Review Daily Breakdown
```

Verifikasi schedules, assignments, attendances, activities, violations dan breakdown harian.

## 16. Workflow M — Monthly Excel

```text
Monthly Report
→ Export Excel
```

Workbook harus memiliki:

```text
1. Ringkasan
2. Jadwal
3. Penugasan
4. Kehadiran
5. Aktivitas
6. Pelanggaran
```

Summary dan detail harus konsisten.

## 17. Workflow N — Role Security

Test setiap role:

- allowed action
- forbidden action
- direct URL
- unauthorized POST/PUT/DELETE jika relevan

UI restriction saja tidak cukup; authorization harus enforced server-side.

## 18. Workflow O — Tenant Isolation

Dengan Tenant A dan B:

```text
Tenant A login → hanya data A
Tenant B login → hanya data B
```

Test:

- student
- schedule
- assignment
- attendance
- activity
- violation
- report
- evidence/file
- direct ID/URL manipulation
- school_id manipulation

## 19. Workflow P — Mobile Real-World

Prioritas viewport:

```text
360px
390px
412px
```

Simulasi:

```text
PKS di lapangan
→ HP
→ Login
→ Schedule
→ Assignment
→ Attendance
→ Field Activity
→ Violation
```

Acceptance:

- tidak ada page-level horizontal overflow
- button mudah disentuh
- form nyaman
- table usable
- modal tidak terpotong
- filter usable
- navigation usable

## 20. Workflow Q — Negative Testing

Sengaja uji:

- duplicate NIS
- duplicate violation code
- invalid student/type
- inactive member
- duplicate assignment
- invalid location
- duplicate attendance
- invalid assignment
- ended_at < started_at
- invalid violation
- unauthorized URL
- cross-tenant ID
- invalid/oversized upload

Expected: validation/authorization error yang benar, bukan unexpected 500.

## 21. Workflow R — Empty State

Uji:

- tanggal tanpa schedule
- bulan tanpa activity
- search tanpa hasil
- report tanpa data

Expected: empty state jelas, tidak error dan tidak membocorkan data tenant lain.

## 22. Workflow S — Full Real-World Simulation

### H-1 — School Admin

```text
Login
→ Review Student
→ Review PKS
→ Shift
→ Location
→ Create Schedule
→ Assign PKS
```

### Hari H — Operator

```text
Login
→ Today's Schedule
→ Assignment
→ Attendance
→ Monitoring
→ Field Activity
→ Violation jika ada
→ Complete Activity
```

### Setelah kegiatan — Admin

```text
Daily Report
→ Review
→ Export PDF
```

### Akhir bulan — Admin

```text
Monthly Report
→ Review Summary
→ Daily Breakdown
→ Export Excel
```

Workflow harus selesai tanpa database editing manual.

## 23. Test Record

Setiap test dicatat:

```text
TEST ID
ACTOR
PRECONDITION
ACTION
EXPECTED RESULT
ACTUAL RESULT
STATUS
SEVERITY
NOTES
```

Status:

```text
PASS
FAIL
BLOCKED
NOT APPLICABLE
```

Jika FAIL, tentukan apakah Bug, Requirement Gap, atau Expected Behavior yang belum jelas. Jangan mengubah requirement secara diam-diam.

## 24. Severity

### P0 — BLOCKER
Tenant leak, auth bypass, data loss, critical security. Deployment STOP.

### P1 — CRITICAL
Core workflow/CRUD/attendance/activity/violation/report/PDF/Excel gagal. Deployment STOP.

### P2 — MAJOR
Mobile workflow sulit, filter rusak, UI broken yang mengganggu pekerjaan. Wajib diperbaiki bila workflow utama terdampak.

### P3 — MINOR
Cosmetic, spacing, wording. Dapat menjadi backlog bila tidak mengganggu fungsi.

## 25. AI Agent Guardrail

AI Agent WAJIB:

1. Membaca file ini sebelum bekerja.
2. Memahami workflow bisnis sebelum coding.
3. Audit implementation existing terlebih dahulu.
4. Tidak membuat fitur di luar scope tanpa instruksi.
5. Tidak mengubah business workflow hanya karena implementasi saat ini berbeda.
6. Tidak menghapus feature existing.
7. Tidak menghapus test.
8. Tidak menganggap route terbuka sebagai bukti feature bekerja.
9. Tidak menganggap automated test sebagai satu-satunya bukti.
10. Memastikan perubahan tidak memutus workflow sebelumnya.
11. Menjalankan regression test setelah perubahan.
12. Melaporkan discrepancy secara eksplisit.

Jika Expected berbeda dengan Actual:

```text
REQUIREMENT DISCREPANCY

Expected:
...

Actual:
...

Impact:
...

Recommendation:
...
```

Jangan diam-diam mengubah requirement.

## 26. Definition of Done

- [ ] Authentication
- [ ] Academic workflow
- [ ] Student workflow
- [ ] Violation master
- [ ] PKS workflow
- [ ] Schedule
- [ ] Assignment
- [ ] Attendance
- [ ] Field Activity
- [ ] Violation operational flow
- [ ] Daily Report
- [ ] PDF
- [ ] Monthly Report
- [ ] Excel
- [ ] Role authorization
- [ ] Tenant isolation
- [ ] Negative testing
- [ ] Empty state
- [ ] Mobile workflow
- [ ] Full end-to-end simulation
- [ ] Automated regression tests PASS

## 27. Final Agent Report

Wajib berisi:

### A. Workflow Audit
Workflow yang diperiksa.

### B. Findings

| ID | Workflow | Severity | Expected | Actual | Fix |
|---|---|---|---|---|---|

### C. UAT Result

| Test ID | Actor | Workflow | Status | Notes |
|---|---|---|---|---|

### D. Mobile Result

| Workflow | 360 | 390 | 412 | Desktop | Status |
|---|---|---|---|---|---|

### E. Regression

```text
Tests:
Assertions:
Failed:
```

### F. Remaining Issues

Semua issue yang belum selesai.

### G. Final Decision

Gunakan hanya:

```text
READY FOR DEPLOYMENT
```

atau

```text
NOT READY FOR DEPLOYMENT
```

Jangan menyatakan READY jika masih ada P0/P1 atau critical workflow yang gagal.

## 28. Final Principle

Sistem ini bukan sekadar kumpulan CRUD.

Sistem adalah:

```text
SCHOOL ADMINISTRATION
        +
PKS DAILY OPERATION
        +
DISCIPLINE RECORDING
        +
REPORTING
```

Kualitas sistem dinilai dari pertanyaan:

> Apakah user sekolah dapat menyelesaikan pekerjaan nyata dari awal sampai akhir dengan alur yang masuk akal, data yang konsisten, permission yang aman, dan interface yang nyaman terutama di handphone?

# END OF DOCUMENT
