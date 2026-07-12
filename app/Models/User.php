<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    public function runner()
    {
        return $this->hasOne(Runner::class);
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'requester_id');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRunner(): bool
    {
        return $this->role === 'runner';
    }
}