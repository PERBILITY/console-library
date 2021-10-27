<?php

declare(strict_types=1);

namespace Perbility\Console\Service\Logger\Teams;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Monolog\Logger;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Service\Logger\Teams
 */
class GuzzleTeamsLogHandler extends TeamsLogHandler
{
    /** @var string */
    private string $url;

    /** @var Client */
    private Client $client;

    /**
     * @param Client $client
     * @param string $url
     * @param int $level
     * @param bool $bubble
     * @param int $maxLengthContext
     * @param int $maxLengthMessage
     */
    public function __construct(
        Client $client,
        string $url,
        int $level = Logger::DEBUG,
        bool $bubble = true,
        int $maxLengthContext = MsTeamsMonologServiceProvider::DEFAULT_TEAMS_MAX_LENGTH_CONTEXT,
        int $maxLengthMessage = MsTeamsMonologServiceProvider::DEFAULT_TEAMS_MAX_LENGTH_MESSAGE
    ) {
        parent::__construct($url, $level, $bubble, $maxLengthContext, $maxLengthMessage);
        
        $this->url = $url;
        $this->client = $client;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     *
     * @return void
     */
    protected function write(array $record): void
    {
        $this->client->post($this->url, [
            RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
            RequestOptions::BODY => json_encode($this->getMessage($record))
        ]);
    }
}
