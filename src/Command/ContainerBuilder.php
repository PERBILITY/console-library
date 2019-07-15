<?php
namespace Perbility\Console\Command;

use Pimple\Container;
use Symfony\Component\Console\Application;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Command
 */
abstract class ContainerBuilder
{
    /** @var Container */
    private $pimple;
    
    /** @var Application */
    private $application;
    
    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }
    
    /**
     * @param Container $pimple
     * @param Application $application
     */
    abstract protected function initContainer(Container $pimple, Application $application);
    
    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->pimple) {
            $this->pimple = new Container();
            $this->initContainer($this->pimple, $this->application);
        }
        return $this->pimple;
    }
    
    /**
     * @param string $location
     * @return string
     */
    public static function injectHome($location)
    {
        if ($location[0] !== '~') {
            return $location;
        }
        return getenv('HOME') . substr($location, 1);
    }
}
