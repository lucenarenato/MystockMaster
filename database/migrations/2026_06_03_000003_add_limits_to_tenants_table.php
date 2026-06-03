<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedInteger('max_users')->nullable()->after('ordem');
            $table->unsignedInteger('max_products')->nullable()->after('max_users');
            $table->unsignedInteger('max_sales')->nullable()->after('max_products');
            $table->unsignedInteger('max_purchases')->nullable()->after('max_sales');
            $table->unsignedInteger('max_storage_mb')->nullable()->after('max_purchases');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'max_users',
                'max_products',
                'max_sales',
                'max_purchases',
                'max_storage_mb',
            ]);
        });
    }
};
