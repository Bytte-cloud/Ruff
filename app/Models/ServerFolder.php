<?php

namespace Ruff\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * \Ruff\Models\ServerFolder.
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $name
 * @property string|null $color
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property User $user
 * @property \Illuminate\Database\Eloquent\Collection|Server[] $servers
 */
class ServerFolder extends Model
{
    public const RESOURCE_NAME = 'server_folder';

    protected $table = 'server_folders';

    protected $fillable = [
        'uuid',
        'name',
        'color',
        'sort',
    ];

    protected $casts = [
        'sort' => 'integer',
    ];

    public static array $validationRules = [
        'name' => ['required', 'string', 'max:191'],
        'color' => ['nullable', 'string', 'max:32'],
        'sort' => ['sometimes', 'integer', 'min:0'],
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = Uuid::uuid4()->toString();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The servers that have been placed into this folder by the owning user.
     */
    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'server_folder_servers', 'folder_id', 'server_id')
            ->withTimestamps();
    }
}
