<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->decimal('opening_cash', 10, 2)->default(0);
            $table->decimal('closing_cash', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cashier_user_id', 'started_at']);
            $table->index('ended_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_cashier_shifts');
    }
};
