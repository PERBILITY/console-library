<?php
namespace Perbility\Console\Command;

use Pimple\Container;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Command
 */
class Command extends BaseCommand
{
    /** @var Container */
    protected $container;
    
    /**
     * @param Container $container
     * @param string|null $name
     */
    public function __construct(Container $container, string $name = null)
    {
        parent::__construct($name);
        $this->container = $container;
    }
    
    public function __get($name)
    {
        return $this->container[$name];
    }
}
