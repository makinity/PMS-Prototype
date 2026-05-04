<?php

use App\Models\Ipcr;
use App\Models\Opcr;
use App\Models\IpcrItem;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$opcr = Opcr::first();
$mfoTitles = [];
$sources = $opcr->sourceUnitWorkPlans();
foreach ($sources as $uwp) {
    foreach ($uwp->uwpFunctions as $f) {
        foreach ($f->mfos as $mfo) {
            $mfoTitles[] = trim($mfo->title);
        }
    }
}

$denji = \App\Models\User::where('name', 'like', '%Denji%')->first();
$denjiIpcr = Ipcr::where('employee_id', $denji->id)->where('opcr_id', $opcr->id)->first();

echo "Comparing Denji's Titles against OPCR MFOs:\n\n";

if ($denjiIpcr) {
    $items = IpcrItem::where('ipcr_id', $denjiIpcr->id)->get();
    foreach ($items as $item) {
        $denjiTitle = trim($item->output_title);
        $foundMatch = false;
        foreach ($mfoTitles as $mfoTitle) {
            if ($denjiTitle === $mfoTitle) {
                echo "✓ MATCH: '{$denjiTitle}'\n";
                $foundMatch = true;
                break;
            }
            if (strtolower($denjiTitle) === strtolower($mfoTitle)) {
                echo "✗ CASE MISMATCH: Denji has '{$denjiTitle}', OPCR has '{$mfoTitle}'\n";
                $foundMatch = true;
                break;
            }
        }
        if (!$foundMatch) {
            echo "!! NO MATCH FOUND for Denji's: '{$denjiTitle}'\n";
            // Check for hidden chars
            echo "   Denji Title Length: " . strlen($denjiTitle) . "\n";
            echo "   Available MFO Titles: " . implode(", ", array_map(fn($t) => "'$t' (" . strlen($t) . ")", $mfoTitles)) . "\n";
        }
    }
}
