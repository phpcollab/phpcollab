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
     * @param string $language Language code (e.g., 'en', 'fr', 'de')
     * @return SymfonyContainerBuilder
     * @throws \Exception
     */
    public static function create(array $configuration, string $language = 'en'): SymfonyContainerBuilder
    {
        $container = new SymfonyContainerBuilder();

        // Set configuration parameters
        $container->setParameter('database.config', $configuration);
        $container->setParameter('app.language', $language);

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
     * @param string $language Language code (e.g., 'en', 'fr', 'de')
     * @return Container
     * @throws \Exception
     */
    public static function createLegacyContainer(array $configuration, string $language = 'en'): Container
    {
        $symfonyContainer = self::create($configuration, $language);

        // Create the legacy Container and inject the Symfony container
        $legacyContainer = new Container($configuration);
        $legacyContainer->setSymfonyContainer($symfonyContainer);
        $legacyContainer->setLanguage($language);

        // Set the container as service locator (for backward compatibility)
        $symfonyContainer->set('service_locator.container', $legacyContainer);

        return $legacyContainer;
    }
}
