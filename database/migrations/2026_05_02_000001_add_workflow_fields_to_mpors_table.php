<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpors', function (Blueprint $table) {
            if (!Schema::hasColumn('mpors', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('submitted_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('mpors', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('mpors', 'endorsed_by')) {
                $table->foreignId('endorsed_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('mpors', 'endorsed_at')) {
                $table->timestamp('endorsed_at')->nullable()->after('endorsed_by');
            }

            if (!Schema::hasColumn('mpors', 'returned_by')) {
                $table->foreignId('returned_by')->nullable()->after('endorsed_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('mpors', 'returned_at')) {
                $table->timestamp('returned_at')->nullable()->after('returned_by');
            }

            if (!Schema::hasColumn('mpors', 'return_remarks')) {
                $table->text('return_remarks')->nullable()->after('returned_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mpors', function (Blueprint $table) {
            if (Schema::hasColumn('mpors', 'return_remarks')) {
                $table->dropColumn('return_remarks');
            }

            if (Schema::hasColumn('mpors', 'returned_at')) {
                $table->dropColumn('returned_at');
            }

            if (Schema::hasColumn('mpors', 'returned_by')) {
                $table->dropConstrainedForeignId('returned_by');
            }

            if (Schema::hasColumn('mpors', 'endorsed_at')) {
                $table->dropColumn('endorsed_at');
            }

            if (Schema::hasColumn('mpors', 'endorsed_by')) {
                $table->dropConstrainedForeignId('endorsed_by');
            }

            if (Schema::hasColumn('mpors', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('mpors', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }
        });
    }
};
