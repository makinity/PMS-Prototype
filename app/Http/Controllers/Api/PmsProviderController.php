<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\PerformancePeriod;
use App\Models\User;
use Illuminate\Http\JsonResponse;

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
}
