<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('runner_id')->nullable()->constrained('runners')->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->string('document_type');
            $table->string('pickup_location');
            $table->string('delivery_address');
            $table->enum('status', [
                'requested', 'approved', 'assigned', 'picked_up',
                'in_transit', 'delivered', 'rejected'
            ])->default('requested');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_requests');
    }
};