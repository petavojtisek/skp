<?php

namespace App\Model\System;

class Slack
{
    private string $webhookUrl = 'tvoje_url_ze_slacku';

    public function sendMessage(string $text): void
    {
        $client = new Client();
        $client->post($this->webhookUrl, [
            'json' => ['text' => $text]
        ]);
    }
}
