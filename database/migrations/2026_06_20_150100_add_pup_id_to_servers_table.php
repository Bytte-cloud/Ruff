<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * A VPS server is backed by a Pup instead of a Nest/Egg. To allow that we
     * add a nullable pup_id foreign key and relax the egg_id/nest_id columns to
     * be nullable. Existing (Docker) servers always carry an egg/nest, so
     * widening these columns to nullable never violates their foreign keys.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->unsignedInteger('pup_id')->nullable()->after('egg_id');

            $table->unsignedInteger('egg_id')->nullable()->change();
            $table->unsignedInteger('nest_id')->nullable()->change();
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->foreign('pup_id')->references('id')->on('pups')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign(['pup_id']);
            $table->dropColumn('pup_id');
        });

        // Note: egg_id/nest_id are intentionally left nullable on rollback —
        // restoring NOT NULL would fail if any VPS servers (null egg/nest) exist.
    }
};
