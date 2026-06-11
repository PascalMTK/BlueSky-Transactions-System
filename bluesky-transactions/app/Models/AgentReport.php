<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AgentReport extends Model
{
    protected $fillable = ['agent_id', 'subject', 'message', 'status', 'admin_reply', 'replied_at'];

    protected $casts = ['replied_at' => 'datetime'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
