# SCHOOL DISCIPLINE SaaS

## MASTER DEVELOPMENT INSTRUCTION FOR AI AGENT

## 1. PROJECT OBJECTIVE

Build a production-oriented, multi-tenant SaaS platform for schools to manage student discipline, student violations, PKS activities, attendance, counseling follow-up, and automated reporting.

The current manual workflow consists of:

* PKS monitoring students at school gates, parking areas, toilets, mosque, and other locations.
* Recording student violations manually.
* Reporting incidents manually through WhatsApp groups.
* Preparing daily reports manually.
* Preparing monthly Excel recaps manually.
* Managing PKS duty schedules manually.
* Recording PKS attendance manually.

The system must digitalize these workflows.

The product must be designed as a SaaS from the beginning so multiple schools can use the same application while their data remains completely isolated.

---

# 2. CORE PRODUCT PRINCIPLES

The system must prioritize:

1. Simple user experience.
2. Mobile-first interface.
3. Fast page loading.
4. Responsive design.
5. Secure multi-tenancy.
6. Maintainable Laravel architecture.
7. Minimal unnecessary JavaScript.
8. Strong database integrity.
9. Automated reporting.
10. Easy future scaling.

The application must NOT be over-engineered.

Do not introduce microservices unless there is a concrete technical requirement.

Use a modular monolith architecture.

---

# 3. TECHNOLOGY STACK

Backend:

* Laravel
* PHP 8.3+
* PostgreSQL

Frontend:

* Blade
* Livewire
* Bootstrap

Infrastructure:

* Nginx
* Redis
* Supervisor
* HTTPS

Supporting services:

* Queue
* Scheduler
* Object/file storage

PDF:

* Laravel-compatible PDF generation library.

Excel:

* Laravel Excel / PhpSpreadsheet.

Use stable, well-maintained packages.

Do not add dependencies without a clear reason.

---

# 4. ARCHITECTURE

Use a modular monolith.

Logical modules:

* Authentication
* School/Tenant
* User Management
* Academic Management
* Student Management
* Discipline Management
* PKS Management
* Attendance Management
* Counseling Management
* Reporting
* Dashboard
* Subscription/Billing
* Audit Log

The initial implementation should focus only on the modules required by the current development phase.

Do NOT implement future modules prematurely.

---

# 5. MULTI-TENANCY

The application is a SaaS.

Every school is a tenant.

Example:

School A:

* students
* classes
* users
* violations
* PKS
* reports

School B:

* students
* classes
* users
* violations
* PKS
* reports

School A must NEVER be able to access School B data.

Every tenant-owned database record must contain a school/tenant identifier where appropriate.

Tenant isolation must be enforced at the application/service/query authorization level.

Never rely only on frontend filtering for tenant security.

Test tenant isolation explicitly.

A user must not be able to access another school's data by manually changing:

* URL IDs
* route parameters
* request parameters
* hidden form values
* API parameters

---

# 6. USER EXPERIENCE

The primary field user is PKS staff using a smartphone.

Therefore:

* Mobile-first.
* Large touch targets.
* Simple navigation.
* Minimal typing.
* Fast student search.
* Fast violation recording.
* Camera/photo upload support.
* Clear success/error feedback.
* Avoid unnecessary modal nesting.
* Avoid desktop-only interfaces.

The violation recording process should ideally take less than one minute.

---

# 7. INITIAL USER ROLES

Design the authorization system to support:

SUPER ADMIN

* Platform-level management.

SCHOOL ADMIN

* Manage school data.
* Manage users.
* Manage students.
* Manage discipline configuration.
* View reports.

PKS

* Record violations.
* View assigned duty schedule.
* Perform duty attendance.
* View relevant student information.

TEACHER/BK

* View student discipline history.
* View relevant reports.
* Manage counseling/follow-up when implemented.

Do not give every role full access.

Use explicit authorization.

---

# 8. CORE BUSINESS ENTITIES

The system is expected to eventually contain:

schools

users

academic_years

departments

classes

students

violation_types

violations

violation_evidences

pks_members

duty_shifts

duty_locations

duty_assignments

duty_attendances

counseling_records

reports

audit_logs

subscriptions

payments

The exact schema must be designed carefully during the relevant phase.

Do not create unnecessary tables without justification.

---

# 9. STUDENT DATA

A student should have:

* NIS/NISN where applicable
* Name
* Gender
* Class
* Department
* Academic year
* Active/inactive status
* Optional parent/guardian information

Students must belong to exactly one school tenant.

Student search must support fast lookup by:

* Name
* NIS
* NISN where available
* Class

---

# 10. DISCIPLINE SYSTEM

The violation system must be configurable.

Do not hard-code violation types.

Example:

* Late arrival
* Incomplete uniform
* Missing school attributes
* Truancy
* Leaving class during lesson
* Leaving school without permission
* Unsafe driving
* No helmet
* Improper parking
* Fighting
* Smoking
* Other school-defined violations

Each violation type may have:

* Name
* Description
* Category
* Severity
* Point value
* Active/inactive status

The school must be able to configure its own violation types and points.

---

# 11. VIOLATION RECORD

A violation should record:

* Student
* Violation type
* Date
* Time
* Location
* Reporting officer/user
* Description
* Evidence/photo
* Point value snapshot
* Status where applicable

The system should preserve the point value at the time of the incident so historical records do not change unexpectedly when the master violation configuration changes later.

---

# 12. REPORTING

Eventually the system must generate:

DAILY PDF:

* School information
* Reporting date
* Summary
* Violation list
* Student
* Class
* Violation
* Time
* Location
* Officer
* Optional evidence

MONTHLY EXCEL:

* Detailed violations
* Student recap
* Class recap
* Violation category recap
* Point recap
* Monthly statistics

Reports must be generated from database records rather than manually entered content.

---

# 13. PKS MANAGEMENT

Eventually support:

* PKS member database
* Duty shifts
* Duty locations
* Duty schedules
* Member assignments
* Coordinator designation
* Duty attendance
* Start attendance
* End attendance
* Attendance status

Example locations:

* Front Gate
* Back Gate
* Front Parking
* Back Parking
* Mosque
* TKJ Toilet
* MP Toilet
* Other configurable locations

Do not hard-code these locations.

---

# 14. PERFORMANCE

Optimize for low-resource VPS deployment.

Use:

* Database indexes
* Pagination
* Eager loading
* Query optimization
* Caching where useful
* Queue for heavy jobs
* Lazy loading where appropriate
* Image compression/resizing
* Efficient database queries

Never load thousands of records into memory when pagination/filtering can be used.

Avoid N+1 queries.

Do not load unnecessary JavaScript libraries.

---

# 15. MOBILE/PWA

The application should eventually support PWA behavior.

The primary objective is that PKS can:

* Open the system from smartphone.
* Add it to the home screen.
* Quickly access violation recording.
* Quickly perform attendance.
* Use camera upload.

PWA implementation can be introduced in the dedicated mobile/PWA phase.

---

# 16. SECURITY

Implement:

* HTTPS in production.
* Password hashing.
* CSRF protection.
* Request validation.
* Authorization policies.
* Tenant isolation.
* Rate limiting where appropriate.
* Secure file upload.
* File type validation.
* File size limits.
* Audit logs for sensitive actions.
* Database backups.

Never trust:

* hidden inputs
* route IDs
* frontend permissions
* client-side validation

All critical authorization must happen server-side.

---

# 17. DEVELOPMENT RULES

Before modifying code:

1. Inspect the existing project.
2. Understand current architecture.
3. Identify existing conventions.
4. Avoid unnecessary rewrites.
5. Reuse existing components when appropriate.
6. Make small, focused changes.

Never blindly overwrite existing files.

Never delete working functionality without explicit justification.

Do not implement future features simply because they might be useful.

---

# 18. TESTING REQUIREMENTS

Every major phase must include:

* Feature tests.
* Validation tests.
* Authorization tests.
* Tenant isolation tests.
* Important business logic tests.

At minimum verify:

1. Correct user can access the feature.
2. Unauthorized role is rejected.
3. School A cannot access School B data.
4. Invalid data is rejected.
5. Valid data is saved correctly.
6. Existing features continue working.

Run the relevant test suite after implementation.

Fix failures before declaring the phase complete.

---

# 19. DATABASE RULES

Use:

* Foreign keys.
* Proper indexes.
* Appropriate nullable constraints.
* Appropriate unique constraints.
* Proper cascading behavior.

Do not use arbitrary VARCHAR columns everywhere.

Choose data types based on actual requirements.

Use database constraints where they improve integrity.

---

# 20. UI RULES

The interface should be:

* Clean.
* Modern.
* Professional.
* School-friendly.
* Mobile responsive.
* Accessible.
* Consistent.

Do not create visually excessive dashboards.

Prioritize useful information over decoration.

Use reusable UI components.

---

# 21. DEVELOPMENT PHILOSOPHY

Build the smallest correct version first.

Do not build:

* AI features prematurely.
* Complex analytics prematurely.
* Native mobile applications prematurely.
* Microservices prematurely.
* Kubernetes prematurely.
* Complex billing infrastructure before product validation.

The immediate objective is a working MVP that can be tested by a real school.

---

# 22. PHASE EXECUTION RULE

The project will be developed phase-by-phase.

When instructed to implement a specific phase:

1. Audit the current project.
2. Explain the implementation plan.
3. Implement only the requested phase.
4. Run tests.
5. Check migrations.
6. Check routes.
7. Check authorization.
8. Check tenant isolation.
9. Check responsive UI.
10. Report all modified files.
11. Report all database changes.
12. Report tests executed and results.
13. Report known limitations.
14. DO NOT continue automatically into the next phase.

Wait for explicit instruction before starting the next phase.

---

# 23. REPORT FORMAT

After every implementation phase, provide:

## IMPLEMENTATION REPORT

### 1. Objective

What was implemented.

### 2. Database Changes

Tables, columns, indexes, relationships.

### 3. Backend Changes

Controllers, models, services, policies, jobs, etc.

### 4. Frontend Changes

Views, Livewire components, UI components.

### 5. Routes

New or modified routes.

### 6. Authorization

Roles and permissions implemented.

### 7. Tenant Isolation

How tenant isolation was enforced and tested.

### 8. Testing

Tests executed and results.

### 9. Manual Testing

Exact steps to manually test the feature.

### 10. Files Changed

List important files.

### 11. Known Issues

Any remaining issues.

### 12. Next Recommended Phase

Recommend the next phase, but DO NOT implement it automatically.

---

# 24. MOST IMPORTANT RULE

Do not optimize for writing the most code.

Optimize for:

CORRECTNESS
+
SECURITY
+
SIMPLICITY
+
MAINTAINABILITY
+
REAL USER VALUE

The goal is to build a real SaaS product that can eventually serve hundreds of schools using one maintainable codebase.
