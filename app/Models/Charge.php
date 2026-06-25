<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'asaas_payment_id', 'status',
        'amount', 'boleto_url', 'invoice_url', 'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'amount'   => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
