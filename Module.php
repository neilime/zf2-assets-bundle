<?php

namespace AssetsBundle;

class Module implements
\Zend\ModuleManager\Feature\ConfigProviderInterface, \Zend\ModuleManager\Feature\AutoloaderProviderInterface, \Zend\ModuleManager\Feature\ConsoleUsageProviderInterface {

    /**
     * @param \Zend\EventManager\EventInterface $oEvent
     */
    public function onBootstrap(\Zend\EventManager\EventInterface $oEvent) {
        $oApplication = $oEvent->getApplication();

        //Attach AssesBundle service events
        $oApplication->getEventManager()->attach($oApplication->getServiceManager()->get('AssetsBundleService'));
    }

    /**
     * @see \Zend\ModuleManager\Feature\ConsoleUsageProviderInterface::getConsoleUsage()
     * @param \Zend\Console\Adapter\AdapterInterface $oConsole
     * @return array
     */
    public function getConsoleUsage(\Zend\Console\Adapter\AdapterInterface $oConsole) {
        return array(
            'Rendering assets:',
            'render' => 'render all assets',
            'Empty cache:',
            'empty' => 'empty cache directory'
        );
    }

    /**
     * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
     * @return array
     */
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . DIRECTORY_SEPARATOR . 'autoload_classmap.php'
            )
        );
    }

    /**
     * @return array
     */
    public function getConfig() {
        return include __DIR__ . DIRECTORY_SEPARATOR . 'config/module.config.php';
    }

}
