<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
