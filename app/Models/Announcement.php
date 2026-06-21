<?php

namespace Ruff\Models;

/**
 * @property int $id
 * @property string $type
 * @property string $title
 * @property string $body
 * @property string|null $link
 * @property bool $enabled
 * @property int $sort_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Announcement extends Model
{
    public const RESOURCE_NAME = 'announcement';

    /**
     * The card types and how each renders on the client dashboard. The key is
     * stored on the model; tag/cls/offer are derived for the frontend.
     */
    public const TYPES = [
        'offer' => ['tag' => 'Offer', 'cls' => 'o', 'offer' => true],
        'announcement' => ['tag' => 'Announcement', 'cls' => 'a', 'offer' => false],
        'blog' => ['tag' => 'Blog', 'cls' => 'b', 'offer' => false],
    ];

    protected $table = 'announcements';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static array $validationRules = [
        'type' => 'required|string|in:offer,announcement,blog',
        'title' => 'required|string|max:191',
        'body' => 'required|string',
        'link' => 'nullable|string|max:191',
        'enabled' => 'sometimes|boolean',
        'sort_order' => 'sometimes|integer',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return $this->getKeyName();
    }

    /**
     * The presentation metadata (tag label, CSS class, offer flag) for this
     * announcement's type, used when transforming for the client dashboard.
     */
    public function presentation(): array
    {
        return self::TYPES[$this->type] ?? self::TYPES['announcement'];
    }
}
