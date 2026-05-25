<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->text('descricao')->nullable();
            // Banner reference: keep as nullable unsignedBigInteger to avoid
            // cross-table constraint ordering issues during migrations.
            $table->unsignedBigInteger('banner_id')->nullable()->index();
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
