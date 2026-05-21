<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'user_id', 'label', 'phone', 'message', 'url', 'clicks'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hits()
    {
        return $this->hasMany(LinkHit::class);
    }
}
