<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[Fillable('history_id', 'contact_id', 'name', 'mobile_num', 'status', 'error_message', 'sent_at')]
class Recipients extends Model
{
    use SoftDeletes;

    const STATUS_QUEUED = 'queued';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime'
        ];
    }

    public function getHashIdAttribute(): string
    {
        return Hashids::encode($this->id);
    }

    public function history(): BelongsTo
    {
        return $this->belongsTo(History::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public static function findByhashId(string $hash): self
    {
        $id = Hashids::decode($hash);

        if (!$id) 
        {
            throw (new ModelNotFoundException)->setModel(static::class);
        }

        return static::query()->findOrFail($id[0]);
    }
}
