<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ALL IPCRs ===" . PHP_EOL;
foreach (App\Models\Ipcr::with('employee')->get() as $i) {
    echo "  id:{$i->id} emp:{$i->employee->name} status:{$i->status} final_score:{$i->final_score} adj_rating:{$i->adjectival_rating} released_at:{$i->released_at}" . PHP_EOL;
}

echo PHP_EOL . "=== IPCRs with STATUS_RELEASED_BY_PMT ===" . PHP_EOL;
$count = App\Models\Ipcr::where('status', App\Models\Ipcr::STATUS_RELEASED_BY_PMT)->count();
echo "  count: {$count}" . PHP_EOL;
echo "  STATUS_RELEASED_BY_PMT constant = '" . App\Models\Ipcr::STATUS_RELEASED_BY_PMT . "'" . PHP_EOL;
