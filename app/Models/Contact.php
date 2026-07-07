<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['phone_num', 'contact_name', 'tags'])]
class Contact extends Model
{
    protected function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }

    public static function allTags()
    {
        return static::query()
            ->pluck('tags')
            ->filter()
            ->flatMap(fn ($tags) => $tags)
            ->unique()
            ->sort()
            ->values();
    }
}
