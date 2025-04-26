<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'date',
        'time',
        'event_type_id',
        'event_for',
        'event_guidelines',
        'created_by',
    ];

    public function eventFor()
    {
        return $this->belongsTo(User::class, 'event_for');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_user', 'event_id', 'user_id')->withPivot('status');
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function requisitionList()
    {
        return $this->hasOne(RequisitionList::class);
    }

    public function requisitionItems()
    {
        return $this->hasManyThrough(RequisitionItem::class, RequisitionList::class);
    }

    public function photos()
    {
        return $this->hasMany(EventPhoto::class);
    }

    public function hasExpired(): bool
    {
        return Carbon::parse($this->date)->isPast();
    }
}
