<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentPlan;
use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Models\TopPerformer;
use App\Models\User;
use App\Services\DevelopmentPlanningService;
use App\Services\StageFourPerformerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PmsProviderController extends Controller
{
    public function employees(): JsonResponse
    {
        $employees = User::query()
            ->with('office:id,name,code')
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'employee_id' => $user->employee_id,
                    'hms_employee_id' => $user->hms_employee_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'office_id' => $user->office_id,
                    'office_name' => $user->office?->name,
                    'office_code' => $user->office?->code,
                    'position' => $user->position,
                    'is_active' => (bool) $user->is_active,
                    'activated_at' => optional($user->activated_at)?->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'data' => $employees,
        ]);
    }

    public function offices(): JsonResponse
    {
        $offices = Office::query()
            ->with('head:id,name')
            ->orderBy('name')
            ->get()
            ->map(function (Office $office) {
                return [
                    'id' => $office->id,
                    'code' => $office->code,
                    'name' => $office->name,
                    'head_id' => $office->head_id,
                    'head_name' => $office->head?->name,
                ];
            })
            ->values();

        return response()->json([
            'data' => $offices,
        ]);
    }

    public function performancePeriods(): JsonResponse
    {
        $periods = PerformancePeriod::query()
            ->orderByDesc('start_date')
            ->get()
            ->map(function (PerformancePeriod $period) {
                return [
                    'id' => $period->id,
                    'name' => $period->name,
                    'start_date' => optional($period->start_date)?->toDateString(),
                    'end_date' => optional($period->end_date)?->toDateString(),
                    'is_active' => (bool) $period->is_active,
                ];
            })
            ->values();

        return response()->json([
            'data' => $periods,
        ]);
    }

    public function topPerformers(Request $request, StageFourPerformerService $performerService): JsonResponse
    {
        $requestedPeriodId = $request->integer('performance_period_id');
        $period = null;

        if ($requestedPeriodId > 0) {
            $period = PerformancePeriod::query()->find($requestedPeriodId);
        }

        if (! $period) {
            $period = PerformancePeriod::query()
                ->where('is_active', true)
                ->orderByDesc('start_date')
                ->first();
        }

        $performerService->syncTopPerformers($period);

        $query = TopPerformer::query()
            ->with('performancePeriod:id,name')
            ->orderByDesc('performance_period_id')
            ->orderBy('performer_type')
            ->orderBy('rank');

        $performerType = trim((string) $request->query('performer_type', ''));
        if (in_array($performerType, [TopPerformer::TYPE_EMPLOYEE, TopPerformer::TYPE_OFFICE], true)) {
            $query->where('performer_type', $performerType);
        }

        if ($requestedPeriodId > 0) {
            $query->where('performance_period_id', $requestedPeriodId);
        } elseif ($period) {
            $query->where('performance_period_id', $period->id);
        }

        $rows = $query->get()->map(function (TopPerformer $row) {
            return [
                'id' => $row->id,
                'performer_type' => $row->performer_type,
                'source_record_id' => $row->source_record_id,
                'employee_id' => $row->employee_id,
                'office_id' => $row->office_id,
                'performance_period_id' => $row->performance_period_id,
                'performance_period_name' => $row->performancePeriod?->name,
                'rank' => $row->rank,
                'performer_name' => $row->performer_name,
                'surname' => $row->surname,
                'given_name' => $row->given_name,
                'middle_name' => $row->middle_name,
                'name_extension' => $row->name_extension,
                'designation' => $row->designation,
                'office_name' => $row->office_name,
                'department_head_name' => $row->department_head_name,
                'official_score' => round((float) $row->official_score, 2),
                'official_rating' => $row->official_rating,
                'remarks' => $row->remarks,
                'released_at' => optional($row->released_at)?->toISOString(),
            ];
        })->values();

        return response()->json([
            'data' => $rows,
        ]);
    }

    public function idpList(Request $request, DevelopmentPlanningService $planningService): JsonResponse
    {
        $requestedPeriodId = $request->integer('performance_period_id');
        $period = null;

        if ($requestedPeriodId > 0) {
            $period = PerformancePeriod::query()->find($requestedPeriodId);
        }

        if (! $period) {
            $period = PerformancePeriod::query()
                ->where('is_active', true)
                ->orderByDesc('start_date')
                ->first();
        }

        $query = DevelopmentPlan::query()
            ->with([
                'employee.office:id,name',
                'office.head:id,name',
                'performancePeriod:id,name',
                'creator:id,name',
                'updater:id,name',
            ])
            ->orderByDesc('performance_period_id')
            ->orderBy('status')
            ->orderBy('employee_id');

        $status = trim((string) $request->query('status', ''));
        if (in_array($status, [
            DevelopmentPlan::STATUS_DRAFT,
            DevelopmentPlan::STATUS_PENDING_DETAILS,
            DevelopmentPlan::STATUS_SUBMITTED_TO_LD,
        ], true)) {
            $query->where('status', $status);
        }

        if ($requestedPeriodId > 0) {
            $query->where('performance_period_id', $requestedPeriodId);
        } elseif ($period) {
            $query->where('performance_period_id', $period->id);
        }

        return response()->json([
            'data' => $planningService->mapPersistedDevelopmentPlans($query->get()),
        ]);
    }
}
