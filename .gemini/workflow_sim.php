<?php
$supervisor = App\Models\User::where('email', 'carlo.beray@example.com')->first();
$deptHead = App\Models\User::where('email', 'dept-head.rcu@example.com')->first();
$pmt = App\Models\User::where('email', 'pmt@example.com')->first();

$uwp = App\Models\UnitWorkPlan::where('created_by', $supervisor->id)->first();

// 1. Submit UWP
$uwp->update(['status' => 'submitted', 'submitted_at' => now()]);
echo "UWP Submitted\n";

// 2. Endorse UWP (Dept Head)
$uwp->update(['status' => 'endorsed', 'endorsed_at' => now(), 'endorsed_by' => $deptHead->id]);
echo "UWP Endorsed\n";

// 3. Consolidate to OPCR
$opcr = App\Models\Opcr::updateOrCreate(
    ['office_id' => $uwp->office_id, 'performance_period_id' => $uwp->performance_period_id],
    [
        'status' => 'draft',
        'generated_by' => $deptHead->id,
    ]
);
$opcr->unitWorkPlans()->syncWithoutDetaching([$uwp->id]);
echo "OPCR Created/Consolidated\n";

// 4. Endorse OPCR (Dept Head)
$opcr->update(['status' => 'endorsed', 'submitted_at' => now(), 'approved_by' => $deptHead->id]);
echo "OPCR Endorsed\n";

// 5. Approve OPCR (PMT)
$opcr->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => $pmt->id]);
echo "OPCR Approved by PMT\n";

// 6. Simulate Ratings & Accomplishments
$employees = App\Models\User::where('office_id', $uwp->office_id)->where('role', 'employee')->get();
foreach ($employees as $index => $emp) {
    $ipcr = App\Models\Ipcr::updateOrCreate(
        ['employee_id' => $emp->id, 'opcr_id' => $opcr->id],
        [
            'office_id' => $opcr->office_id,
            'unit_work_plan_id' => $uwp->id,
            'performance_period_id' => $opcr->performance_period_id,
            'status' => 'released_by_pmt',
            'final_score' => 4.0 + ($index * 0.2), // 4.0, 4.2, 4.4
            'adjectival_rating' => 'Very Satisfactory',
        ]
    );
    echo "Created IPCR for {$emp->name} with score {$ipcr->final_score}\n";
}

// Update OPCR with final score based on IPCRs
$opcr->update([
    'final_score' => $employees->avg('final_score') ?? 4.2,
    'adjectival_rating' => 'Very Satisfactory',
    'status' => 'released_by_pmt'
]);
echo "OPCR Finalized and Released\n";
