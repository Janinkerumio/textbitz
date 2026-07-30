<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[Fillable('user_id', 'template_id', 'blast', 'status', 'recipients', 'sent_count', 'failed_count' , 'type', 'send_mode', 'slug', 'scheduled_at',  'last_sent_at')]
class History extends Model
{
    use SoftDeletes;

    protected $table = 'history';

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_QUEUED = 'queued';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'last_sent_at' => 'datetime',
            'recipients' => 'integer',
            'sent_count' => 'integer',
            'failed_count' => 'integer'
        ];
    }

    public function getHashIdAttribute(): string
    {
        return Hashids::encode($this->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipients::class);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
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
