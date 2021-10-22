<?php

declare(strict_types=1);

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
    public const CONTEXT_KEY_ESCAPE_TO_MARKDOWN = 'escape_to_markdown';

    /**
     * @var int
     */
    protected int $maxLengthContext;

    /**
     * @var int
     */
    protected int $maxLengthMessage;

    /**
     * @var array
     */
    protected static array $levelColors = [
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
     * @param string $url
     * @param int $level
     * @param bool $bubble
     * @param int $maxLengthContext
     * @param int $maxLengthMessage
     */
    public function __construct(
        string $url,
        int $level = Logger::DEBUG,
        bool $bubble = true,
        int $maxLengthContext = 500,
        int $maxLengthMessage = 20000
    ) {
        parent::__construct($url, $level, $bubble);

        $this->maxLengthContext = $maxLengthContext;
        $this->maxLengthMessage = $maxLengthMessage;
    }

    /**
     * @param array $record
     *
     * @return TeamsMessage
     */
    protected function getMessage(array $record): TeamsMessage
    {
        $convertToMarkdown = $record['context'][self::CONTEXT_KEY_ESCAPE_TO_MARKDOWN] ?? false;
        if ($convertToMarkdown) {
            $record['message'] = $this->escapeToMarkdown($record['message']);
        }

        if (strlen($record['message']) > $this->maxLengthMessage) {
            $record['message'] = '[Message too long, truncated] '
                . substr($record['message'], 0, $this->maxLengthMessage)
                . ' …';
        }

        $sections = [
            [
                "text" => $record['level_name'] . ': ' . $record['message'],
                "markdown" => $convertToMarkdown,
            ],
        ];

        if (!empty($record['context'])) {
            unset($record['context'][self::CONTEXT_KEY_ESCAPE_TO_MARKDOWN]);

            $payload = json_encode($record['context']);
            if (strlen($payload) > $this->maxLengthContext) {
                $payload = '[Context too long, deleted]';
            }

            $sections[] = [
                "text" => $payload,
                "markdown" => false,
            ];
        }

        return new TeamsMessage([
            "summary" => $record['level_name'],
            "themeColor" => self::$levelColors[$record['level']] ?? self::$levelColors[$this->level],
            "sections" => $sections,
        ]);
    }

    /**
     * @param string $message
     *
     * @return string
     */
    protected function escapeToMarkdown(string $message): string
    {
        return str_replace(
            ["\\", "_", "*", "#", "-", "+", "[", "]", "  ", "\n"],
            ["\\\\", "\\_", "\\*", "\\#", "\\-", "\\+", "\\[", "\\]", "&ensp;&ensp;", "  \n"],
            $message
        );
    }
}
