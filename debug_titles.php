<?php

use App\Models\Ipcr;
use App\Models\Opcr;
use App\Models\IpcrItem;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$opcr = Opcr::first();
echo "--- OPCR MFO Titles ---\n";
$sources = $opcr->sourceUnitWorkPlans();
foreach ($sources as $uwp) {
    foreach ($uwp->uwpFunctions as $f) {
        foreach ($f->mfos as $mfo) {
            echo "- '" . trim($mfo->title) . "'\n";
        }
    }
}

echo "\n--- Denji Kun's IPCR Output Titles ---\n";
$denjiIpcr = Ipcr::where('employee_id', 2)->where('opcr_id', $opcr->id)->first(); // Denji is ID 3 in DB but 2 in my user search? Wait.
// Let's just find Denji again correctly.
$denji = \App\Models\User::where('name', 'like', '%Denji%')->first();
$denjiIpcr = Ipcr::where('employee_id', $denji->id)->where('opcr_id', $opcr->id)->first();

if ($denjiIpcr) {
    $items = IpcrItem::where('ipcr_id', $denjiIpcr->id)->get();
    foreach ($items as $item) {
        echo "- '" . trim($item->output_title) . "'\n";
    }
} else {
    echo "No IPCR found for Denji Kun.\n";
}
