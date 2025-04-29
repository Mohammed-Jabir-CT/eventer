<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $fillable = [
        'requisition_list_id',
        'name',
        'availability',
        'is_optional',
        'claimed_by'
    ];

    public function requisitionList()
    {
        return $this->belongsTo(RequisitionList::class);
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }
}
