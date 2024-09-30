<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('data_folder_location');
            $table->string('gitlab_url')->nullable();
            $table->string('gitlab_username')->nullable();
            $table->string('gitlab_personal_access_token')->nullable();
            $table->string('ediarum_backend_url')->nullable();
            $table->string('ediarum_backend_api_key')->nullable();
            $table->string('exist_base_url')->nullable();
            $table->string('exist_data_path')->nullable();
            $table->string('exist_username')->nullable();
            $table->string('exist_password')->nullable();
            $table->timestamps();

        });


        Schema::create('project_user', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
        Schema::dropIfExists('projects');
    }
};
