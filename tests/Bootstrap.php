<?php

namespace AssetsBundleTest;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap {

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected static $serviceManager;

    /**
     * @var array
     */
    protected static $config;

    /**
     * Initialize bootstrap
     */
    public static function init() {
        //Load the user-defined test configuration file, if it exists;
        $aTestConfig = include is_readable(__DIR__ . '/TestConfig.php') ? __DIR__ . '/TestConfig.php' : __DIR__ . '/TestConfig.php.dist';
        $aZf2ModulePaths = array();
        if (isset($aTestConfig['module_listener_options']['module_paths'])) {
            foreach ($aTestConfig['module_listener_options']['module_paths'] as $sModulePath) {
                if (($sPath = static::findParentPath($sModulePath))) {
                    $aZf2ModulePaths[] = $sPath;
                }
            }
        }
        static::initAutoloader();

        //Use ModuleManager to load this module and it's dependencies
        static::$config = \Zend\Stdlib\ArrayUtils::merge(array(
                    'module_listener_options' => array(
                        'module_paths' => $aZf2ModulePaths
                    )
                        ), $aTestConfig);
        static::$serviceManager = new \Zend\ServiceManager\ServiceManager(new \Zend\Mvc\Service\ServiceManagerConfig());
        static::$serviceManager->setService('ApplicationConfig', static::$config)->get('ModuleManager')->loadModules();
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public static function getServiceManager() {
        return static::$serviceManager;
    }

    /**
     * @return array
     */
    public static function getConfig() {
        return static::$config;
    }

    /**
     * Initialize Autoloader
     * @throws \RuntimeException
     */
    protected static function initAutoloader() {
        //Composer autoloading
        if (file_exists($sAutoloadPath = static::findParentPath('vendor') . DIRECTORY_SEPARATOR . 'autoload.php')) {
            include $sAutoloadPath;
        } else {
            throw new \LogicException('Autoload file "' . $sAutoloadPath . '" does not exist');
        }
        if (!class_exists('Zend\Loader\AutoloaderFactory')) {
            throw new \RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
        }

        //Autoload test classes
        \Zend\Loader\AutoloaderFactory::factory(array(
            \Zend\Loader\AutoloaderFactory::STANDARD_AUTOLOADER => array(
                \Zend\Loader\StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__ . DIRECTORY_SEPARATOR . __NAMESPACE__
                )
            )
        ));
    }

    /**
     * Retrieve parent for a given path
     * @param string $sPath
     * @return boolean|string
     */
    protected static function findParentPath($sPath) {
        $sCurrentDir = __DIR__;
        $sPreviousDir = '.';
        while (!is_dir($sPreviousDir . '/' . $sPath)) {
            $sCurrentDir = dirname($sCurrentDir);
            if ($sPreviousDir === $sCurrentDir) {
                return false;
            }
            $sPreviousDir = $sCurrentDir;
        }
        return $sCurrentDir . '/' . $sPath;
    }

}

\AssetsBundleTest\Bootstrap::init();
