<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['unit_work_plans','uwp_functions','uwp_mfos','uwp_success_indicators','uwp_qet_standards','uwp_indicator_assignments','opcrs'];
foreach ($tables as $t) {
    echo "\n### {$t}\n";
    try {
        $rows = Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `{$t}`");
        if (!$rows) { echo "(no rows)\n"; continue; }
        $row = (array) $rows[0];
        $create = $row['Create Table'] ?? array_values($row)[1] ?? '';
        echo $create . "\n";
    } catch (Throwable $e) {
        echo 'ERROR: ' . $e->getMessage() . "\n";
    }
}
