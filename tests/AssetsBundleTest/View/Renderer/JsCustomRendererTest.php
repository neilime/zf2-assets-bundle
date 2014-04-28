<?php

namespace AssetsBundleTest\View\Renderer;

class JsCustomRendererTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \AssetsBundle\View\Renderer\JsCustomRenderer
     */
    protected $jsCustomRenderer;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp() {
        $this->jsCustomRenderer = new \AssetsBundle\View\Renderer\JsCustomRenderer();
    }

    public function testGetEngine() {
        $this->assertEquals($this->jsCustomRenderer, $this->jsCustomRenderer->getEngine());
    }

    public function testSetResolver() {
        $this->assertEquals($this->jsCustomRenderer, $this->jsCustomRenderer->setResolver(\AssetsBundleTest\Bootstrap::getServiceManager()->get('ViewResolver')));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRenderWithWrongViewModel() {
        $this->jsCustomRenderer->render('wrong');
    }

}
