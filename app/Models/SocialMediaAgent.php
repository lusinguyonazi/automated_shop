<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaAgent extends Model
{
    use HasFactory;

    protected $table = 'social_media_agents';

    protected $fillable = ['user_id', 'agent_code',];
}
