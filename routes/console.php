<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pms:refresh-dev {--keep-evidence : Skip ORS evidence cleanup}', function () {
    $evidenceDisk = Storage::disk('public');
    $evidenceDirectory = 'ors_evidences';
    $evidenceRoot = storage_path('app/public/' . $evidenceDirectory);

    if (!$this->option('keep-evidence')) {
        if ($evidenceDisk->exists($evidenceDirectory)) {
            $this->components->task('Deleting ORS evidence files', function () use ($evidenceDisk, $evidenceDirectory) {
                return $evidenceDisk->deleteDirectory($evidenceDirectory);
            });
        }

        $photoDirectory = 'profile_photos';
        if ($evidenceDisk->exists($photoDirectory)) {
            $this->components->task('Deleting profile photos', function () use ($evidenceDisk, $photoDirectory) {
                return $evidenceDisk->deleteDirectory($photoDirectory);
            });
        }

        File::ensureDirectoryExists($evidenceRoot);
    } else {
        $this->warn('Skipping ORS evidence cleanup (--keep-evidence).');
    }

    $this->call('migrate:fresh', [
        '--seed' => true,
        '--force' => true,
    ]);
})->purpose('Refresh the local PMS database and clear ORS evidence files.');
