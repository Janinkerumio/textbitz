<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Collection;

#[Fillable('category', 'title', 'message', 'variables', 'tags', 'icon', 'color')]
class Template extends Model
{
    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'tags' => 'array'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }

    public static function totalTemplatesCount(): int
    {
        return static::query()
            ->get()
            ->count();
    }

    public static function mostUsed(): Collection
    {
        return static::query()
            ->withCount('histories')
            ->orderByDesc('histories_count')
            ->limit(15)
            ->get();
    }
}
