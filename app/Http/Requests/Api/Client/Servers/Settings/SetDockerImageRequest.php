<?php

namespace Ruff\Http\Requests\Api\Client\Servers\Settings;

use Ruff\Models\Server;
use Ruff\Models\Permission;
use Webmozart\Assert\Assert;
use Illuminate\Validation\Rule;
use Ruff\Contracts\Http\ClientPermissionsRequest;
use Ruff\Http\Requests\Api\Client\ClientApiRequest;

class SetDockerImageRequest extends ClientApiRequest implements ClientPermissionsRequest
{
    public function permission(): string
    {
        return Permission::ACTION_STARTUP_DOCKER_IMAGE;
    }

    public function rules(): array
    {
        /** @var Server $server */
        $server = $this->route()->parameter('server');

        Assert::isInstanceOf($server, Server::class);

        // VPS servers have no egg and therefore no Docker image whitelist; the
        // controller rejects the change for them, so just validate the format here.
        $allowedImages = array_values($server->egg?->docker_images ?? []);

        return [
            'docker_image' => ['required', 'string', 'max:191', 'regex:/^[\w#\.\/\- ]*\|?~?[\w\.\/\-:@ ]*$/', Rule::in($allowedImages)],
        ];
    }
}
