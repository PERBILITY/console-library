<?php
namespace Perbility\Console\Service\Store;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Service\Store
 */
class StoreNotInitializedException extends \RuntimeException
{
    /**
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message = 'Local store is not initialized', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
