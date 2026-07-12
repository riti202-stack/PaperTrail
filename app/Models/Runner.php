<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Runner extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'vehicle_no',
        'is_available', 'current_lat', 'current_lng', 'location_updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'runner_zone');
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }
}