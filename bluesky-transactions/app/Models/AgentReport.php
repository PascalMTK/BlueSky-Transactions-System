<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AgentReport extends Model
{
    protected $fillable = ['agent_id', 'subject', 'message', 'status'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
