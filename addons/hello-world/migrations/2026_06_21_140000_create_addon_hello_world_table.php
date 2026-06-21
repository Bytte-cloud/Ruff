<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Example addon migration. Run when the addon is enabled and rolled back when it
| is disabled — so it must have a working down(). It lives under the addon, so
| the panel's normal `php artisan migrate` ignores it unless the addon is enabled.
*/
return new class extends Migration {
    public function up(): void
    {
        Schema::create('addon_hello_world_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_hello_world_log');
    }
};
