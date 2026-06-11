<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('role')->default('agent')->after('phone');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete()->after('role');
            $table->string('agent_code', 20)->unique()->nullable()->after('country_id');
            $table->string('status')->default('pending')->after('agent_code');
            $table->string('address')->nullable()->after('status');
            $table->string('id_number', 50)->nullable()->after('address');
            $table->string('profile_photo')->nullable()->after('id_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['phone', 'role', 'country_id', 'agent_code', 'status', 'address', 'id_number', 'profile_photo']);
        });
    }
};
