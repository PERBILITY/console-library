<?php

declare(strict_types=1);

namespace Perbility\Console\Service\Logger\Teams;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Monolog\Logger;
use Perbility\Console\Service\Logger\TargetMappingLogger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\NullLogger;

/**
 * A provider that adds logging handlers to MS-Teams to a logger.
 *
 * @author Sven Hüßner <sven.huessner@perbility.de>
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Service\Logger\Teams
 */
class MsTeamsMonologServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        // Define a logger to be added to $app and store it as a service
        $app['msteams'] = $app->factory(
            function () use ($app) {
                if (empty($app['msteams.webhooks'])) {
                    return new NullLogger();
                }

                $log = new TargetMappingLogger();
                $app['msteams.configure']($log);
                return $log;
            }
        );

        // Define handlers for the above created logger and store them as configurations
        $app['msteams.configure'] = $app->protect(
            function (TargetMappingLogger $targetLogger) use ($app) {
                $webhookConfigs = $app['msteams.webhooks'];

                // Handler defaults
                $defaults = [
                    'name' => 'MS Teams',
                    'level' => Logger::INFO,
                    'bubble' => true,
                    'maxLengthContext' => 500,
                    'maxLengthMessage' => 20000,
                ];

                if (isset($webhookConfigs['_default'])) {
                    $defaults = $webhookConfigs['_default'] + $defaults;
                    unset($webhookConfigs['_default']);
                }

                // Create handlers for the logger according to config
                foreach ($webhookConfigs as $webhookConfig) {
                    $webhookConfig += $defaults;
                    $logger = new Logger('hcm');

                    if (isset($webhookConfig["guzzle"]) && is_array($webhookConfig["guzzle"])) {
                        // For clusters behind a proxy we need a Guzzle handler
                        $logger->pushHandler(
                            new GuzzleTeamsLogHandler(
                                new Client($this->prepareGuzzleOptions($webhookConfig['guzzle'])),
                                $webhookConfig['url'],
                                Logger::toMonologLevel($webhookConfig['level']),
                                $webhookConfig['bubble'],
                                $webhookConfig['maxLengthContext'],
                                $webhookConfig['maxLengthMessage']
                            )
                        );
                    } else {
                        $logger->pushHandler(
                            new TeamsLogHandler(
                                $webhookConfig['url'],
                                Logger::toMonologLevel($webhookConfig['level']),
                                $webhookConfig['bubble'],
                                $webhookConfig['maxLengthContext'],
                                $webhookConfig['maxLengthMessage']
                            )
                        );
                    }

                    $targetLogger->addLogger($logger, $webhookConfig['targets']);
                }
            }
        );
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function prepareGuzzleOptions(array $config): array
    {
        $options = [];

        // add curl proxy options
        if (!empty($config['proxy']) && $config['proxy']['host']) {
            $url = $config['proxy']['host'];

            if ($config['proxy']['port']) {
                $url .=  ':' . $config['proxy']['port'];
            }

            $options[RequestOptions::PROXY] = [
                'http' => $url,
                'https' => $url,
            ];
        }

        // add ssl config
        if ($config['verify_ssl'] === false) {
            $options[RequestOptions::VERIFY] = false;
        }

        return $options;
    }
}
