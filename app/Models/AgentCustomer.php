<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentCustomer extends Model
{
    use HasFactory;
    protected $fillable = ['agent_id','user_id', 'agent_code',];
}
