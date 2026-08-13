<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[Fillable('history_id', 'contact_id', 'remote_id', 'name', 'mobile_num', 'status', 'sync_status', 'error_message', 'sent_at')]
class Recipients extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_QUEUED = 'queued';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    const SYNC_STATUS_SYNCED = 'synced';
    const SYNC_STATUS_PENDING = 'pending';
    const SYNC_STATUS_FAILED = 'failed';

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

    public function scopeForUser(Builder $query, ?int $userId = null): Builder
    {
        return $query->whereHas('history', fn ($qu) => $qu->where('user_id', $userId ?? Auth::id()));
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
