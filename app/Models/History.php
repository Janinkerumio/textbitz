<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

#[Fillable('user_id', 'template_id', 'blast', 'status', 'recipients', 'last_sent_at')]
class History extends Model
{
    protected $table = 'history';

    protected function casts(): array
    {
        return [
            'recipients' => 'array',
        ];
    }

    public function getHashIdAttribute(): string
    {
        return Hashids::encode($this->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public static function initiateQuery(): Builder
    {
        return static::query()->where('user_id', Auth::id());
    }

    public static function isUserHasSavedHistory(): bool
    {
        return static::initiateQuery()
            ->exists();
    }

    public static function stats(): Collection
    {
        return static::initiateQuery()
            ->select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->orderBy('status', 'desc')
            ->get();
    }
}
