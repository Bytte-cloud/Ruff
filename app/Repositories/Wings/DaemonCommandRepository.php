<?php

namespace Ruff\Repositories\Wings;

use Ruff\Models\Server;
use Webmozart\Assert\Assert;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\TransferException;
use Ruff\Exceptions\Http\Connection\DaemonConnectionException;

/**
 * @method \Ruff\Repositories\Wings\DaemonCommandRepository setNode(\Ruff\Models\Node $node)
 * @method \Ruff\Repositories\Wings\DaemonCommandRepository setServer(\Ruff\Models\Server $server)
 */
class DaemonCommandRepository extends DaemonRepository
{
    /**
     * Sends a command or multiple commands to a running server instance.
     *
     * @throws DaemonConnectionException
     */
    public function send(array|string $command): ResponseInterface
    {
        Assert::isInstanceOf($this->server, Server::class);

        try {
            return $this->getHttpClient()->post(
                sprintf('/api/servers/%s/commands', $this->server->uuid),
                [
                    'json' => ['commands' => is_array($command) ? $command : [$command]],
                ]
            );
        } catch (TransferException $exception) {
            throw new DaemonConnectionException($exception);
        }
    }
}
