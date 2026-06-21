<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * VPS (QEMU) servers have no egg variables, so there is nowhere to put the
     * cloud-init provisioning inputs the daemon needs (VM_PASSWORD, VM_SSH_KEYS,
     * VM_USER, VM_HOSTNAME). Store them as a small JSON map on the server; the
     * panel emits them into the environment sent to the daemon.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->json('vm_options')->nullable()->after('pup_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn('vm_options');
        });
    }
};
