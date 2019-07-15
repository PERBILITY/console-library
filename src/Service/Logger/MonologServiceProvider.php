<?php
namespace Perbility\Console\Service\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Monolog Provider.
 *
 * This class is an adaptation of the Silex MonologServiceProvider written by
 * Fabien Potencier.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Mike van Riel <mike.vanvriel@naenius.com>
 */
class MonologServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['monolog'] = $pimple->factory(
            function () use ($pimple) {
                $log = new Logger(isset($pimple['monolog.name']) ? $pimple['monolog.name'] : 'myapp');
                $pimple['monolog.configure']($log);

                return $log;
            }
        );

        $pimple['monolog.configure'] = $pimple->protect(
            function (Logger $log) use ($pimple) {
                $log->pushHandler($pimple['monolog.handler']);
            }
        );

        $pimple['monolog.handler'] = function () use ($pimple) {
            return new StreamHandler($pimple['monolog.logfile'], $pimple['monolog.level']);
        };

        if (!isset($pimple['monolog.level'])) {
            $pimple['monolog.level'] = function () {
                return Logger::DEBUG;
            };
        }

        if (isset($pimple['monolog.class_path'])) {
            $pimple['autoloader']->registerNamespace('Monolog', $pimple['monolog.class_path']);
        }
    }
}
