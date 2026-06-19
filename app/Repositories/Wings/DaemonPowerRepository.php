<?php

namespace Ruff\Repositories\Wings;

use Ruff\Models\Server;
use Webmozart\Assert\Assert;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\TransferException;
use Ruff\Exceptions\Http\Connection\DaemonConnectionException;

/**
 * @method \Ruff\Repositories\Wings\DaemonPowerRepository setNode(\Ruff\Models\Node $node)
 * @method \Ruff\Repositories\Wings\DaemonPowerRepository setServer(\Ruff\Models\Server $server)
 */
class DaemonPowerRepository extends DaemonRepository
{
    /**
     * Sends a power action to the server instance.
     *
     * @throws DaemonConnectionException
     */
    public function send(string $action): ResponseInterface
    {
        Assert::isInstanceOf($this->server, Server::class);

        try {
            return $this->getHttpClient()->post(
                sprintf('/api/servers/%s/power', $this->server->uuid),
                ['json' => ['action' => $action]]
            );
        } catch (TransferException $exception) {
            throw new DaemonConnectionException($exception);
        }
    }
}
