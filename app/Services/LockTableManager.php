<?php

namespace App\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LockTableManager
{
    public static function ensureTableForProject(int $projectId): void
    {
        $table = 'locks_' . $projectId;

        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                $table->string("owner", 200);
                $table->unsignedInteger("timeout");
                $table->integer("created");
                $table->string("token", 200)->index();
                $table->tinyInteger('scope');
                $table->tinyInteger('depth');
                $table->string('uri')->index();
            });
        }
    }

    public static function dropTableForProject(int $projectId): void
    {
        $table = 'locks_' . $projectId;

        if (Schema::hasTable($table)) {
            Schema::dropIfExists($table);
        }
    }

}
