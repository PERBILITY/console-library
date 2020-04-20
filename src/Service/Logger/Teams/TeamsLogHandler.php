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
    const CONTEXT_KEY_ESCAPE_TO_MARKDOWN = 'escape_to_markdown';

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
        if ($record['context'][self::CONTEXT_KEY_ESCAPE_TO_MARKDOWN] ?? false) {
            $sections = [[
                "text" => $record['level_name'] . ': ' . $this->escapeToMarkdown($record['message']),
                "markdown" => true,
            ]];
        } else {
            $sections = [[
                "text" => $record['level_name'] . ': ' . $record['message'],
                "markdown" => false,
            ]];
        }
        if (!empty($record['context'])) {
            unset($record['context'][self::CONTEXT_KEY_ESCAPE_TO_MARKDOWN]);
            $sections[] = [
                "text" => json_encode($record['context']),
                "markdown" => false,
            ];
        }
        return new TeamsMessage([
            "summary" => $record['level_name'],
            "themeColor" => self::$levelColors[$record['level']] ?? self::$levelColors[$this->level],
            "sections" => $sections,
        ]);
    }
    
    protected function escapeToMarkdown($message) {
        return str_replace(
            ["\\", "_", "*", "#", "-", "+", "[", "]", "  ", "\n"],
            ["\\\\", "\\_", "\\*", "\\#", "\\-", "\\+", "\\[", "\\]", "&ensp;&ensp;", "  \n"],
            $message
        );
    }
}
