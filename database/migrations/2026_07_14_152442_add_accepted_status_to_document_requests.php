<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status ENUM(
            'requested','approved','assigned','accepted','picked_up','in_transit','delivered','rejected'
        ) DEFAULT 'requested'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status ENUM(
            'requested','approved','assigned','picked_up','in_transit','delivered','rejected'
        ) DEFAULT 'requested'");
    }
};