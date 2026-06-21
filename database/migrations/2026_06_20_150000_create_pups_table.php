<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * "Pups" are the VPS analogue of Eggs: a pre-determined catalogue of VM
     * templates (Ubuntu, Debian, Windows, ...) each pointing at a base image
     * (qcow2 path / template filename / http(s) URL) and describing the guest
     * OS family and firmware the daemon should boot it with.
     */
    public function up(): void
    {
        Schema::create('pups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            // Maps to the daemon "vm.os" label: "linux" or "windows".
            $table->string('os_type')->default('linux');
            // Maps to the daemon "vm.firmware" label: "bios" or "uefi".
            // Null lets the daemon infer it (Windows -> UEFI, Linux -> BIOS).
            $table->string('firmware')->nullable();
            // Base/template image: an absolute path, a filename under the daemon
            // template directory, or an http(s):// URL downloaded on first boot.
            $table->string('image_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pups');
    }
};
