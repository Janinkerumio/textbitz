<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

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

    public function mostUsed()
    {
        //query the most used template
    }
}
