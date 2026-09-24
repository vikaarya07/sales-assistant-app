<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'phone_normalized',
        'name',
        'contract_number',
        'amount',
        'branch',
        'source',
        'status',
        'last_contacted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'last_contacted_at' => 'datetime',
            'status' => CustomerStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format(
            $this->amount,
            0,
            ',',
            '.'
        );
    }

    public function getWhatsappUrlAttribute(): string
    {
        return 'https://wa.me/'.$this->phone_normalized;
    }

    /**
     * @param  array<string, mixed>  $variables
     */
    public function replaceTemplateVariables(
        string $template,
        array $variables = []
    ): string {
        $replacements = [
            '{{nama}}' => $this->name,
            '{{nomor_kontrak}}' => $this->contract_number,
            '{{nominal}}' => number_format($this->amount, 0, ',', '.'),
            '{{cabang}}' => $this->branch,
        ];

        $replacements = array_merge(
            $replacements,
            $variables
        );

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }

    public function whatsappUrlWithMessage(string $message): string
    {
        return 'https://api.whatsapp.com/send?phone='
            .$this->phone_normalized
            .'&text='
            .rawurlencode($message);
    }
}
