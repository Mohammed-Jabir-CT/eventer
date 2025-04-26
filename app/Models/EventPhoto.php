<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPhoto extends Model
{
    protected $fillable = ['event_id', 'uploaded_by', 'file_path'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
