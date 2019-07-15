<?php
namespace Perbility\Console\Service\Store;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Service\Store
 */
interface LockInterface
{
    /**
     * @return bool
     */
    public function isLocked();
    
    /**
     * @throws LockException
     */
    public function lock();
    
    /**
     */
    public function unlock();
}
