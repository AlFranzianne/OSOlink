<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();

                // Employment/pay fields
                $table->string('employment_status')->nullable();
                $table->decimal('base_pay', 12, 2)->default(0);
                $table->decimal('hourly_rate', 12, 2)->default(0);
                $table->decimal('hours_worked', 8, 2)->default(0);

                // Payroll amounts
                $table->decimal('gross_pay', 12, 2)->default(0);
                $table->decimal('deductions', 12, 2)->default(0);
                $table->decimal('net_pay', 12, 2)->default(0);

                $table->string('status')->default('Processed');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};