<?php

namespace phpCollab;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * Factory for creating and configuring the Symfony DI Container
 */
class ContainerFactory
{
    /**
     * Build and compile the DI container
     *
     * @param array $configuration Application configuration
     * @return SymfonyContainerBuilder
     * @throws \Exception
     */
    public static function create(array $configuration): SymfonyContainerBuilder
    {
        $container = new SymfonyContainerBuilder();

        // Set configuration parameters
        $container->setParameter('database.config', $configuration);

        // Set APP_ROOT as environment variable for parameter resolution
        if (defined('APP_ROOT')) {
            $_ENV['APP_ROOT'] = APP_ROOT;
        }

        // Load service definitions
        $loader = new PhpFileLoader($container, new FileLocator(APP_ROOT . '/config'));
        $loader->load('services.php');

        // Compile the container for better performance
        $container->compile();

        return $container;
    }

    /**
     * Create a Container wrapper around the Symfony DI container for backward compatibility
     *
     * @param array $configuration Application configuration
     * @return Container
     * @throws \Exception
     */
    public static function createLegacyContainer(array $configuration): Container
    {
        $symfonyContainer = self::create($configuration);

        // Create the legacy Container and inject the Symfony container
        $legacyContainer = new Container($configuration);
        $legacyContainer->setSymfonyContainer($symfonyContainer);

        // Set the container as service locator (for backward compatibility)
        $symfonyContainer->set('service_locator.container', $legacyContainer);

        return $legacyContainer;
    }
}
