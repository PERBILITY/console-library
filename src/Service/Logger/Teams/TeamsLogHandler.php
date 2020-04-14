<?php
namespace Perbility\Console\Service\Logger\Teams;

use CMDISP\MonologMicrosoftTeams\TeamsLogHandler as MSTeamsLogHandler;
use CMDISP\MonologMicrosoftTeams\TeamsMessage;
use Monolog\Logger;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Service\Logger\Teams
 */
class TeamsLogHandler extends MSTeamsLogHandler
{
    /**
     * @var array
     */
    protected static $levelColors = [
        Logger::DEBUG => '0080CC',
        Logger::INFO => '00CC00',
        Logger::NOTICE => '00CC00',
        Logger::WARNING => 'CCCC00',
        Logger::ERROR => 'CC0000',
        Logger::CRITICAL => 'CC0000',
        Logger::ALERT => 'CC0000',
        Logger::EMERGENCY => 'CC0000',
    ];
    
    /**
     * @param array $record
     *
     * @return TeamsMessage
     */
    protected function getMessage(array $record)
    {
        $sections = [[ "text" => $record['level_name'] . ": \n```" . $record['message'] . "\n```", "markdown" => true]];
        if (!empty($record['context'])) {
            $sections[] = [ "text" => "\n```" . json_encode($record['context'], JSON_PRETTY_PRINT) . "\n```", "markdown" => true];
        }
        return new TeamsMessage([
            "summary" => $record['level_name'],
            "themeColor" => self::$levelColors[$record['level']] ?? self::$levelColors[$this->level],
            "sections" => $sections,
        ]);
    }
}
