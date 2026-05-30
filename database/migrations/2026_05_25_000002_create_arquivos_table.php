<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('arquivos', function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();
            $table->string('nome', 191);
            $table->string('filename', 191)->nullable();
            $table->string('disk', 64)->default('public');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('caminho')->nullable();
            $table->string('arquivavel_type')->nullable();
            $table->unsignedBigInteger('arquivavel_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arquivos');
    }
};
