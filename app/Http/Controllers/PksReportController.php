<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyPksReportRequest;
use App\Http\Requests\MonthlyPksReportRequest;
use App\Services\TenantContext;
use App\Services\PksReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class PksReportController extends Controller
{
    public function __construct(
        protected PksReportService $reportService,
        protected TenantContext $tenantContext
    ) {}

    /**
     * Display daily report page.
     */
    public function daily(DailyPksReportRequest $request): View
    {
        // Check authorization
        Gate::authorize('viewDailyPksReport');

        $validated = $request->validated();
        $schoolId = $this->resolveSchoolId($request, $validated);
        $date = isset($validated['date'])
            ? Carbon::parse($validated['date'])
            : Carbon::today();

        // Validate school access
        if ($schoolId && !Gate::allows('accessSchoolForReport', [$schoolId])) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        $report = $this->reportService->getDailyReport(
            schoolId: $schoolId,
            date: $date,
            shiftId: $validated['shift_id'] ?? null,
            locationId: $validated['location_id'] ?? null,
            status: $validated['status'] ?? null,
        );

        $shifts = $this->reportService->getShifts($schoolId);
        $locations = $this->reportService->getLocations($schoolId);

        return view('reports.daily', compact(
            'report',
            'shifts',
            'locations',
            'date'
        ));
    }

    /**
     * Generate daily PDF report.
     */
    public function dailyPdf(DailyPksReportRequest $request)
    {
        // Check authorization
        Gate::authorize('exportDailyPdf');

        $validated = $request->validated();
        $schoolId = $this->resolveSchoolId($request, $validated);
        $date = isset($validated['date'])
            ? Carbon::parse($validated['date'])
            : Carbon::today();

        // Validate school access
        if ($schoolId && !Gate::allows('accessSchoolForReport', [$schoolId])) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        $report = $this->reportService->getDailyReport(
            schoolId: $schoolId,
            date: $date,
            shiftId: $validated['shift_id'] ?? null,
            locationId: $validated['location_id'] ?? null,
            status: $validated['status'] ?? null,
        );

        $school = \App\Models\School::find($schoolId);

        $pdf = Pdf::loadView('reports.pdf.daily', [
            'report' => $report,
            'school' => $school,
            'date' => $date,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'laporan-harian-pks-' . $date->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Display monthly report page.
     */
    public function monthly(MonthlyPksReportRequest $request): View
    {
        // Check authorization
        Gate::authorize('viewMonthlyPksReport');

        $validated = $request->validated();
        $schoolId = $this->resolveSchoolId($request, $validated);

        // Validate school access
        if ($schoolId && !Gate::allows('accessSchoolForReport', [$schoolId])) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        $month = $validated['month'] ?? (int) Carbon::now()->format('m');
        $year = $validated['year'] ?? (int) Carbon::now()->format('Y');

        $report = $this->reportService->getMonthlyReport(
            schoolId: $schoolId,
            month: $month,
            year: $year,
            shiftId: $validated['shift_id'] ?? null,
            locationId: $validated['location_id'] ?? null,
        );

        $shifts = $this->reportService->getShifts($schoolId);
        $locations = $this->reportService->getLocations($schoolId);

        return view('reports.monthly', compact(
            'report',
            'shifts',
            'locations',
            'month',
            'year'
        ));
    }

    /**
     * Generate monthly Excel report.
     */
    public function monthlyExcel(MonthlyPksReportRequest $request)
    {
        // Check authorization
        Gate::authorize('exportMonthlyExcel');

        $validated = $request->validated();
        $schoolId = $this->resolveSchoolId($request, $validated);

        // Validate school access
        if ($schoolId && !Gate::allows('accessSchoolForReport', [$schoolId])) {
            abort(403, 'Anda tidak memiliki akses ke sekolah ini.');
        }

        $month = $validated['month'] ?? (int) Carbon::now()->format('m');
        $year = $validated['year'] ?? (int) Carbon::now()->format('Y');

        $report = $this->reportService->getMonthlyReport(
            schoolId: $schoolId,
            month: $month,
            year: $year,
            shiftId: $validated['shift_id'] ?? null,
            locationId: $validated['location_id'] ?? null,
        );

        $school = \App\Models\School::find($schoolId);

        $filename = 'laporan-pks-bulanan-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MonthlyPksReportExport($report, $school),
            $filename
        );
    }

    /**
     * Resolve school ID with proper tenant isolation.
     */
    protected function resolveSchoolId(Request $request, array $validated): ?int
    {
        $user = $request->user();

        if (!$user) {
            return null;
        }

        // Super admin can specify school_id in request
        if ($user->isSuperAdmin() && isset($validated['school_id'])) {
            $requestedSchoolId = (int) $validated['school_id'];

            // Verify super admin has access to this school
            if ($user->canManageSchool($requestedSchoolId)) {
                return $requestedSchoolId;
            }

            // Fall back to user's school_id
            return $user->school_id;
        }

        // Regular users can only access their own school
        return $user->school_id;
    }
}
