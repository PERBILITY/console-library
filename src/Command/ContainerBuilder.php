<?php
namespace Perbility\Console\Command;

use Pimple\Container;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Command
 */
abstract class ContainerBuilder
{
    /** @var Container */
    private $pimple;
    
    /**
     * @param Container $pimple
     */
    abstract protected function initContainer(Container $pimple);
    
    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->pimple) {
            $this->pimple = new Container();
            $this->initContainer($this->pimple);
        }
        return $this->pimple;
    }
}
