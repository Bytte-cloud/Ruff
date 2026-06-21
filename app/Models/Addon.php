<?php

namespace Ruff\Models;

/**
 * Tracks the enabled state of a drop-in addon. The addon's manifest
 * (addons/<id>/addon.json) is the source of truth for its name/version/etc.;
 * this row only records whether it is enabled.
 *
 * @property int $id
 * @property string $addon_id
 * @property string|null $version
 * @property bool $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Addon extends Model
{
    public const RESOURCE_NAME = 'addon';

    protected $table = 'addons';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static array $validationRules = [
        'addon_id' => 'required|string|max:191',
        'version' => 'nullable|string|max:191',
        'enabled' => 'sometimes|boolean',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'addon_id';
    }
}
