<?php
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
    private $url;
    
    /** @var Client */
    private $client;
    
    /**
     * @param Client $client
     * @param $url
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(
        Client $client,
        $url,
        $level = Logger::DEBUG,
        $bubble = true
    ) {
        parent::__construct($url, $level, $bubble);
        
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
