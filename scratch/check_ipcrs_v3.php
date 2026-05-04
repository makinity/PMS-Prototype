<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ipcr;
use App\Models\AccomplishmentSubmission;
use App\Models\User;

$mark = User::where('first_name', 'like', '%Mark%')->where('last_name', 'like', '%Juntilla%')->first();

if (!$mark) {
    echo "Employee not found.\n";
    exit;
}

echo "Employee: {$mark->first_name} {$mark->last_name} (ID: {$mark->id})\n";

// Check IPCrs
$ipcrs = Ipcr::where('employee_id', $mark->id)->get();
echo "Found " . $ipcrs->count() . " IPCR(s) in the database:\n";

foreach ($ipcrs as $i) {
    echo "--- IPCR ID: {$i->id} ---\n";
    echo "  Status: {$i->status}\n";
    echo "  Final Score column: {$i->final_score}\n";
    echo "  Adjusted Score column: " . ($i->pmt_adjusted_score ?? 'NULL') . "\n";
    
    $subs = AccomplishmentSubmission::where('ipcr_id', $i->id)->get();
    echo "  Submissions for this IPCR: " . $subs->count() . "\n";
    foreach($subs as $s) {
        echo "    - Sub ID: {$s->id}, Status: {$s->status}\n";
    }
}
