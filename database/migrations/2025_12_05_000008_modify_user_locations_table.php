<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_locations', function (Blueprint $table) {
            // إضافة الحقول الجديدة
            $table->enum('type', ['home', 'work', 'friend', 'other'])->default('home')->after('address');
            $table->boolean('is_default')->default(false)->after('type');
            
            // جعل address required بدلاً من nullable
            $table->string('address')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_locations', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_default']);
            $table->string('address')->nullable()->change();
        });
    }
};

