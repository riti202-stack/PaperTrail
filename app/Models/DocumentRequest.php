<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    protected $fillable = [
        'requester_id', 'runner_id', 'zone_id', 'document_type',
        'pickup_location', 'delivery_address', 'status', 'assigned_at',
        'archived_at', 'delivery_lat', 'delivery_lng'
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function runner()
    {
        return $this->belongsTo(Runner::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(DocumentStatusHistory::class)->orderBy('changed_at');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }
}