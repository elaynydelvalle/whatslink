<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkHit extends Model
{
    public $timestamps = false;

    protected $fillable = ['link_id', 'ip', 'ua', 'referer'];

    public function link()
    {
        return $this->belongsTo(Link::class);
    }
}
