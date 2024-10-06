<?php

namespace Fluffy\Swoole\Websocket;

use Components\Models\Web\HubMessage;

class HubServer
{
    protected int $workerId;
    protected string $uniqueId;

    public function __construct(private \AppServer $appServer)
    {
        $this->workerId = $appServer->server->worker_id;
        $this->uniqueId = $appServer->uniqueId;
    }

    public function send(string $channel, $data = null)
    {
        $message = new HubMessage();
        $message->channel = $channel;
        $message->data = $data;
        $serialized = json_encode($message);
        foreach ($this->appServer->server->connections as $fd) {
            if ($this->appServer->server->isEstablished($fd)) {
                $this->appServer->server->push($fd, $serialized);
            }
        }
    }
}
