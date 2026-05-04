<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ipcrs', function (Blueprint $table) {
            $table->foreignId('released_by')->nullable()->after('pmt_reviewed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('released_at')->nullable()->after('released_by');
        });

        Schema::table('opcrs', function (Blueprint $table) {
            $table->foreignId('released_by')->nullable()->after('pmt_reviewed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('released_at')->nullable()->after('released_by');
        });
    }

    public function down(): void
    {
        Schema::table('ipcrs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('released_by');
            $table->dropColumn('released_at');
        });

        Schema::table('opcrs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('released_by');
            $table->dropColumn('released_at');
        });
    }
};
