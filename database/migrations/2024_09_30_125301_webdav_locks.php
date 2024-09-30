<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locks', function (Blueprint $table) {
            $table->id();
            $table->string("owner", length: 200);
            $table->unsignedInteger("timeout");
            $table->integer("created");
            $table->string("token", length: 200)->index();
            $table->tinyInteger('scope');
            $table->tinyInteger('depth');
            $table->string('uri')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locks');
    }
};
