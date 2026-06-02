<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('arquivos', function (Blueprint $table) {
            $table->string('sha256', 128)->nullable()->after('nome');
            $table->string('unidade', 64)->nullable()->after('sha256');
            $table->unsignedBigInteger('volume_id')->nullable()->after('unidade');
            $table->unsignedBigInteger('originador')->nullable()->after('volume_id');
            $table->string('arquivo', 191)->nullable()->after('originador');
            $table->unsignedBigInteger('tamanho_original')->nullable()->after('arquivo');
            $table->string('mimetype_original', 100)->nullable()->after('tamanho_original');
            $table->string('extensao_original', 32)->nullable()->after('mimetype_original');
            $table->unsignedBigInteger('tamanho')->nullable()->after('extensao_original');
            $table->string('mimetype', 100)->nullable()->after('tamanho');
            $table->string('extensao', 32)->nullable()->after('mimetype');
            $table->unsignedBigInteger('tipo')->nullable()->after('extensao');
            $table->string('classificacao', 100)->nullable()->after('tipo');
            $table->string('titulo', 191)->nullable()->after('classificacao');
            $table->text('descricao')->nullable()->after('titulo');
            $table->string('especie', 100)->nullable()->after('descricao');
            $table->string('genero', 100)->nullable()->after('especie');
            $table->string('autor', 191)->nullable()->after('genero');
            $table->string('destinatario', 191)->nullable()->after('autor');
            $table->text('extras')->nullable()->after('destinatario');
            $table->date('vencimento')->nullable()->after('extras');
        });

        Schema::table('arquivaveis', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('arquivavel_type');
            $table->string('arquivo_type', 100)->nullable()->after('descricao');
            $table->string('arquivo', 191)->nullable()->after('arquivo_type');
            $table->boolean('aprovado')->default(false)->after('arquivo');
            $table->boolean('ativo')->default(true)->after('aprovado');
            $table->date('data_vencimento')->nullable()->after('ativo');
        });
    }

    public function down(): void
    {
        Schema::table('arquivaveis', function (Blueprint $table) {
            $table->dropColumn([
                'descricao',
                'arquivo_type',
                'arquivo',
                'aprovado',
                'ativo',
                'data_vencimento',
            ]);
        });

        Schema::table('arquivos', function (Blueprint $table) {
            $table->dropColumn([
                'sha256',
                'unidade',
                'volume_id',
                'originador',
                'arquivo',
                'tamanho_original',
                'mimetype_original',
                'extensao_original',
                'tamanho',
                'mimetype',
                'extensao',
                'tipo',
                'classificacao',
                'titulo',
                'descricao',
                'especie',
                'genero',
                'autor',
                'destinatario',
                'extras',
                'vencimento',
            ]);
        });
    }
};
