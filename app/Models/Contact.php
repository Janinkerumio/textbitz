<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[Fillable(['phone_num', 'contact_name', 'tags', 'user_id'])]
class Contact extends Model
{
    protected function casts(): array
    {
        return [
            'tags' => 'array',
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

    public static function initiateQuery(): Builder
    {
        return static::query()->where('user_id', Auth::id());
    }

    public static function findByHashId(string $hash): self
    {
        $id = Hashids::decode($hash);

        if (!$id) 
        {
            throw (new ModelNotFoundException)->setModel(static::class);
        }

        return static::initiateQuery()->findOrFail($id[0]);
    }

    public static function allTags(): Collection
    {
        return static::initiateQuery()
            ->pluck('tags')
            ->filter()
            ->flatMap(fn ($tags) => $tags)
            ->unique()
            ->sort()
            ->values();
    }

    public static function userHasSavedContacts(): int
    {
        return static::initiateQuery()
            ->get()
            ->count();
    }
}
