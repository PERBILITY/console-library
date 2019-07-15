<?php
namespace Perbility\Console\Service\Store;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Service\Store
 */
class PermanentLock implements LockInterface
{
    private $file;
    
    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }
    
    /**
     * @return bool
     */
    public function isLocked()
    {
        return file_exists($this->file);
    }
    
    /**
     */
    public function lock()
    {
        touch($this->file);
    }
    
    /**
     */
    public function unlock()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }
}
