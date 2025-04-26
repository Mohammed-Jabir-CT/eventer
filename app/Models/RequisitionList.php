<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionList extends Model
{
    protected $fillable = [
        'visibility'
    ];

    public function is_public()
    {
        return $this->visibility == 1 ? true : false;
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class);
    }
}
