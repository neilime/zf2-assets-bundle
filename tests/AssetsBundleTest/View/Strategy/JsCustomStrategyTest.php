<?php

namespace AssetsBundleTest\View\Strategy;

class JsCustomStrategyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \AssetsBundle\View\Strategy\JsCustomStrategy
     */
    protected $jsCustomStrategy;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // Empty cache and processed directories
        \AssetsBundleTest\Bootstrap::getServiceManager()->get('AssetsBundleToolsService')->emptyCache(false);
        $this->jsCustomStrategy = new \AssetsBundle\View\Strategy\JsCustomStrategy();
    }

    /**
     * @expectedException LogicException
     */
    public function testGetRendererUnset()
    {
        $this->jsCustomStrategy->getRenderer();
    }

    public function testAttachDetach()
    {
        $oEventManager = \AssetsBundleTest\Bootstrap::getServiceManager()->get('EventManager');

        $this->jsCustomStrategy->attach($oEventManager);
        $this->assertEquals(array('renderer', 'response'), $oEventManager->getEvents());
        $this->jsCustomStrategy->detach($oEventManager);
        $this->assertEquals(array(), $oEventManager->getEvents());
    }

    /**
     * @expectedException LogicException
     */
    public function testGetServiceLocatorUnset()
    {
        $this->jsCustomStrategy->getServiceLocator();
    }

    public function testSelectRenderer()
    {
        $this->jsCustomStrategy->setServiceLocator(\AssetsBundleTest\Bootstrap::getServiceManager())->selectRenderer(new \Zend\View\ViewEvent());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testSelectRendererWithWrongModel()
    {

        //Reset server datas
        $_SESSION = array();
        $_GET = array();
        $_POST = array();
        $_COOKIE = array();

        //Reset singleton
        \Zend\EventManager\StaticEventManager::resetInstance();

        //Do not cache module config on testing environment
        $aApplicationConfig = \AssetsBundleTest\Bootstrap::getConfig();
        if (isset($aApplicationConfig['module_listener_options']['config_cache_enabled'])) {
            $aApplicationConfig['module_listener_options']['config_cache_enabled'] = false;
        }
        \Zend\Console\Console::overrideIsConsole(false);
        $oApplication = \Zend\Mvc\Application::init($aApplicationConfig);
        $oApplication->getEventManager()->detach($oApplication->getServiceManager()->get('SendResponseListener'));

        $oRequest = $oApplication->getRequest();
        $oUri = new \Zend\Uri\Http('/jscustom/AssetsBundleTest\\Controller\\Test/test');

        $oRequest->setMethod(\Zend\Http\Request::METHOD_GET)
                ->setUri($oUri)
                ->setRequestUri($oUri->getPath());

        $oApplication->run();

        $oViewEvent = new \Zend\View\ViewEvent();
        $this->jsCustomStrategy
                ->setServiceLocator($oApplication->getServiceManager())
                ->selectRenderer($oViewEvent->setRequest($oRequest));
    }

    public function tearDown()
    {
        //Empty cache and processed directories
        \AssetsBundleTest\Bootstrap::getServiceManager()->get('AssetsBundleToolsService')->emptyCache(false);
        parent::tearDown();
    }
}
