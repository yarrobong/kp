<?php

namespace App\Models;

class Proposal extends Model
{
    protected $table = 'proposals';

    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'offer_number',
        'offer_date',
        'seller_info',
        'buyer_info',
        'body_html',
        'currency',
        'vat_rate',
        'terms',
        'status',
        'published_at',
    ];

    protected $casts = [
        'offer_date' => 'date',
        'vat_rate' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function items()
    {
        return $this->hasMany(ProposalItem::class)->orderBy('sort_order');
    }

    public function files()
    {
        return $this->hasMany(ProposalFile::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function calculateTotals(): array
    {
        $items = $this->items;
        $subtotal = 0;

        foreach ($items as $item) {
            $itemTotal = $item->quantity * $item->price;
            $discountAmount = $itemTotal * ($item->discount / 100);
            $subtotal += $itemTotal - $discountAmount;
        }

        $vat = $subtotal * ($this->vat_rate / 100);
        $total = $subtotal + $vat;

        return [
            'subtotal' => round($subtotal, 2),
            'vat' => round($vat, 2),
            'total' => round($total, 2),
        ];
    }
}

