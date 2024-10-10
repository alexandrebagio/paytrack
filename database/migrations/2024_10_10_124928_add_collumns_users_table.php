<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'type')) {
                $table->enum('type', ['F', 'J']);
            }

            if (!Schema::hasColumn('users', 'identification_number')) {
                $table->string('identification_number');
            }

            $table->unique(['type', 'identification_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIfExists('type');
            $table->dropIfExists('identification_number');
        });
    }
};
