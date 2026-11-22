<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerStatusAssignment extends Model
{
    protected $table = 'customer_statuses';

    protected $fillable = [
        'customer_id',
        'status',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'status' => CustomerStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
