<?php

namespace App\Models;

use App\Models\Concerns\HasBilingualAttributes;
use Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /** @use HasFactory<FaqFactory> */
    use HasBilingualAttributes, HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function getCategoryAttribute(): string
    {
        return $this->getLocalized('category');
    }

    public function getQuestionAttribute(): string
    {
        return $this->getLocalized('question');
    }

    public function getAnswerAttribute(): string
    {
        return $this->getLocalized('answer');
    }
}
