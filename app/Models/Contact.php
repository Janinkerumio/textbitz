<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

#[Fillable(['phone_num', 'contact_name', 'tags', 'user_id'])]
class Contact extends Model
{
    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function initiateQuery()
    {
        return static::query()->where('user_id', Auth::id());
    }

    public static function allTags()
    {
        return static::initiateQuery()
            ->pluck('tags')
            ->filter()
            ->flatMap(fn ($tags) => $tags)
            ->unique()
            ->sort()
            ->values();
    }

    public static function userHasSavedContacts()
    {
        return static::initiateQuery()
            ->get()
            ->count();
    }
}
