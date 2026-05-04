<?php

use App\Models\Ipcr;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$denji = User::where('name', 'like', '%Denji%')->first();
$ipcr = Ipcr::where('employee_id', $denji->id)->latest()->first();

echo "Denji Kun's IPCR Details:\n";
echo "ID: {$ipcr->id}\n";
echo "Status: {$ipcr->status}\n";
echo "Final Score: " . ($ipcr->final_score ?? 'NULL') . "\n";
echo "PMT Adjusted Score: " . ($ipcr->pmt_adjusted_score ?? 'NULL') . "\n";
