
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice')->unique()->after('id');
            $table->string('courier')->after('snap_token');
            $table->string('courier_service')->after('courier');
            $table->bigInteger('courier_cost')->after('courier_service');
            $table->integer('weight')->after('courier_cost');
            $table->string('name')->after('weight');
            $table->string('phone')->after('name');
            $table->string('province_id')->after('phone');
            $table->string('city_id')->after('province_id');
            $table->text('address')->after('city_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['invoice']);

            $table->dropColumn([
                'invoice',
                'courier',
                'courier_service',
                'courier_cost',
                'weight',
                'name',
                'phone',
                'province_id',
                'city_id',
                'address',
            ]);
        });
    }
};
