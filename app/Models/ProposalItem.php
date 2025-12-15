<?php

namespace App\Models;

class ProposalItem extends Model
{
    protected $table = 'proposal_items';

    protected $fillable = [
        'proposal_id',
        'name',
        'quantity',
        'unit',
        'price',
        'discount',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function getTotalAttribute(): float
    {
        $total = $this->quantity * $this->price;
        return round($total - ($total * $this->discount / 100), 2);
    }
}

