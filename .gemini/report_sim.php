<?php
$activePeriod = App\Models\PerformancePeriod::where('is_active', true)->first();
$performerService = app(App\Services\StageFourPerformerService::class);
$performerService->syncTopPerformers($activePeriod);
$data = $performerService->getPersistedTopPerformers($activePeriod);

echo "--- RESULTS FOR {$activePeriod->name} ---\n\n";

echo "TOP PERFORMING EMPLOYEES:\n";
foreach ($data['top_employees'] as $tp) {
    echo "- {$tp['employee_name']} | Score: {$tp['official_score']} | Rating: {$tp['official_rating']} | Office: {$tp['office_name']}\n";
}

echo "\nLOW PERFORMING EMPLOYEES:\n";
foreach ($data['low_employees'] as $lp) {
    echo "- {$lp['employee_name']} | Score: {$lp['official_score']} | Rating: {$lp['official_rating']} | Office: {$lp['office_name']}\n";
}

echo "\nTOP PERFORMING OFFICES:\n";
foreach ($data['top_offices'] as $to) {
    echo "- {$to['office_name']} | Score: {$to['official_score']} | Rating: {$to['official_rating']} | Head: {$to['department_head_name']}\n";
}
