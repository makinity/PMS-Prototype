<?php

use App\Models\Ipcr;
use App\Models\OrsEntry;
use App\Models\PerformancePeriod;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$denji = \App\Models\User::where('name', 'like', '%Denji%')->first();
$ipcr = Ipcr::where('employee_id', $denji->id)->latest()->first();
$period = $ipcr->performancePeriod;

echo "Performance Period: {$period->start_date} to {$period->end_date}\n";

$entries = OrsEntry::where('ipcr_id', $ipcr->id)->get();
echo "Total Entries for Denji: " . $entries->count() . "\n";

$ratedEntries = $entries->where('status', 'rated');
echo "Rated Entries: " . $ratedEntries->count() . "\n";

$unratedEntries = $entries->where('status', '!=', 'rated');
echo "Unrated Entries: " . $unratedEntries->count() . "\n";
if ($unratedEntries->count() > 0) {
    echo "Sample Unrated Status: " . $unratedEntries->first()->status . "\n";
}

$inRange = 0;
foreach ($entries as $e) {
    if ($e->work_date >= $period->start_date && $e->work_date <= $period->end_date) {
        $inRange++;
    }
}
echo "Entries within Date Range: {$inRange}\n";
