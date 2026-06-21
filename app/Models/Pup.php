<?php

namespace Ruff\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * \Ruff\Models\Pup.
 *
 * A VPS template (the QEMU analogue of an Egg). Describes the base image and the
 * guest OS family / firmware the daemon should boot a virtual machine with.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $os_type
 * @property string|null $firmware
 * @property string $image_url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Ruff\Models\Server[] $servers
 * @property int|null $servers_count
 */
class Pup extends Model
{
    /** @use HasFactory<\Database\Factories\PupFactory> */
    use HasFactory;

    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    public const RESOURCE_NAME = 'pup';

    /**
     * The table associated with the model.
     */
    protected $table = 'pups';

    /**
     * Fields that are not mass assignable.
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Rules ensuring that the raw data stored in the database meets expectations.
     *
     * image_url is constrained to the same character set the servers.image column
     * accepts (see \Ruff\Models\Server) so a Pup image can always be copied onto a
     * VPS server without tripping that column's validation.
     */
    public static array $validationRules = [
        'name' => 'required|string|max:191',
        'description' => 'nullable|string',
        'os_type' => 'required|string|in:linux,windows',
        'firmware' => 'nullable|string|in:bios,uefi',
        'image_url' => ['required', 'string', 'max:191', 'regex:/^~?[\w\.\/\-:@]+$/'],
    ];

    public function getRouteKeyName(): string
    {
        return $this->getKeyName();
    }

    /**
     * Gets the servers (VPS instances) provisioned from this Pup.
     */
    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }
}
