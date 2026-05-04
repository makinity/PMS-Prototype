<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Opcr;

$opcr = Opcr::first();
if ($opcr) {
    $sources = $opcr->sourceUnitWorkPlans();
    echo "Sources Count: " . $sources->count() . "\n";
    echo "Pluck 'unit_work_plans.id':\n";
    print_r($sources->pluck('unit_work_plans.id')->toArray());
    echo "\nPluck 'id':\n";
    print_r($sources->pluck('id')->toArray());
}
