<?php
namespace Projek\Slim;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use InvalidArgumentException;

class MonologProvider implements ServiceProviderInterface
{
    /**
     * Register this monolog provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        $settings = $container->get('settings');

        if (!isset($settings['logger'])) {
            throw new InvalidArgumentException('Logger configuration not found');
        }

        $basename = isset($settings['basename']) ? $settings['basename'] : 'slim-app';

        $container['logger'] = new Monolog($basename, $settings['logger']);
    }
}
