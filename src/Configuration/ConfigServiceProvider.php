<?php
namespace Perbility\Console\Configuration;

use Perbility\Console\Command\ContainerBuilder;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Configuration
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    /** @var string */
    private $filename;
    
    /** @var string[] */
    private $locations;
    
    /** @var mixed[] */
    private $defaults;
    
    /** @var string[][] */
    private $docs;
    
    /**
     * @param string $filename
     * @param string[] $locations
     * @param mixed[] $defaults
     * @param string[][] $docs
     */
    public function __construct(string $filename, array $locations = [], array $defaults = [], array $docs = [])
    {
        $this->filename = $filename;
        $this->locations = $locations;
        $this->defaults = $defaults;
        $this->docs = $docs;
    }
    
    /**
     * @param Container $pimple
     */
    public function register(Container $pimple)
    {
        $pimple['config.file'] = function (Container $c) {
            foreach ($this->locations as $location) {
                $file = sprintf('%s/%s', ContainerBuilder::injectHome($location), $this->filename);
                if (file_exists($file) && is_readable($file)) {
                    return $file;
                }
            }
            return null;
        };
        
        $pimple['config'] = function (Container $c) {
            $config = $this->defaults;
            if ($c['config.file']) {
                $parsed = Yaml::parseFile($c['config.file']) ?? [];
                if (!is_array($parsed)) {
                    throw new \RuntimeException('invalid configuration in ' . $c['config.file']);
                }
                $config = array_merge($config, $parsed);
            }
            return $config;
        };
    }
}
