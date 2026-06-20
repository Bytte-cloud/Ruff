<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Adds an execution-backend selector to servers. "docker" (the default,
     * backfilled onto all existing servers) runs the server as a container;
     * "qemu" runs it as a QEMU/KVM virtual machine (VPS) on the daemon.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->string('environment_type')->default('docker')->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('environment_type');
        });
    }
};
