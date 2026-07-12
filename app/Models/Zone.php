<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name', 'description'];

    public function runners()
    {
        return $this->belongsToMany(Runner::class, 'runner_zone');
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }
}