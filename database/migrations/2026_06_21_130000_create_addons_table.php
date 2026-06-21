<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tracks the install/enabled state of drop-in addons discovered under the
     * project `addons/` directory. The manifest on disk is the source of truth
     * for everything else; this table only records which addons are enabled and
     * the version that was last enabled.
     */
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('addon_id')->unique();
            $table->string('version')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
