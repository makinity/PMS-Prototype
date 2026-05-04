<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ipcr;
use App\Models\AccomplishmentSubmission;

$mark = \App\Models\Employee::where('first_name', 'like', '%Mark%')->where('last_name', 'like', '%Juntilla%')->first();

if (!$mark) {
    echo "Employee not found.\n";
    exit;
}

echo "Employee: {$mark->first_name} {$mark->last_name} (ID: {$mark->id})\n";

$ipcrs = Ipcr::where('employee_id', $mark->id)->get();
echo "Found " . $ipcrs->count() . " IPCR(s):\n";

foreach ($ipcrs as $i) {
    echo "--- IPCR ID: {$i->id} ---\n";
    echo "Status: {$i->status}\n";
    echo "Final Score (in DB): {$i->final_score}\n";
    echo "PMT Adjusted Score: " . ($i->pmt_adjusted_score ?? 'NULL') . "\n";
    
    $submission = AccomplishmentSubmission::where('ipcr_id', $i->id)->first();
    echo "Submission Status: " . ($submission ? $submission->status : 'No submission') . "\n";
}
