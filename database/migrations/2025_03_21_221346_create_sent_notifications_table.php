<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sent_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('notification_id');
            $table->string('channel'); // whatsapp, database, etc.
            $table->string('recipient'); // número de teléfono o identificador
            $table->text('message');
            $table->string('status'); // sent, failed, delivered
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('notification_id')
                ->references('id')
                ->on('notifications')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_notifications');
    }
};
