<?php
return array(
    'AssetsBundle\Module' => __DIR__ . '/Module.php',
	'AssetsBundle\Factory\ServiceFactory' => __DIR__ . '/src/AssetsBundle/Factory/ServiceFactory.php',
	'AssetsBundle\Factory\Filter\CssFilterFactory' => __DIR__ . '/src/AssetsBundle/Factory/Filter/CssFilterFactory.php',
	'AssetsBundle\Factory\Filter\JsFilterFactory' => __DIR__ . '/src/AssetsBundle/Factory/Filter/JsFilterFactory.php',
	'AssetsBundle\Factory\Filter\LessFilterFactory' => __DIR__ . '/src/AssetsBundle/Factory/Filter/LessFilterFactory.php',
	'AssetsBundle\Mvc\Controller\AbstractActionController' => __DIR__ . '/src/AssetsBundle/Mvc/Controller/AbstractActionController.php',
	'AssetsBundle\Service\Filter\CssFilter' => __DIR__ . '/src/AssetsBundle/Service/Filter/CssFilter.php',
	'AssetsBundle\Service\Filter\JsFilter' => __DIR__ . '/src/AssetsBundle/Service/Filter/JsFilter.php',
	'AssetsBundle\Service\Filter\LessFilter' => __DIR__ . '/src/AssetsBundle/Service/Filter/LessFilter.php',
	'AssetsBundle\Service\Filter\FilterInterface' => __DIR__ . '/src/AssetsBundle/Service/Filter/FilterInterface.php',
	'AssetsBundle\Service\Service' => __DIR__ . '/src/AssetsBundle/Service/Service.php',
	'AssetsBundle\View\Renderer\JsRenderer' => __DIR__ . '/src/AssetsBundle/View/Renderer/JsRenderer.php',
	'AssetsBundle\View\Strategy\AbstractStrategy' => __DIR__ . '/src/AssetsBundle/View/Strategy/AbstractStrategy.php',
	'AssetsBundle\View\Strategy\JsCustomStrategy' => __DIR__ . '/src/AssetsBundle/View/Strategy/JsCustomStrategy.php',
	'AssetsBundle\View\Strategy\NoneStrategy' => __DIR__ . '/src/AssetsBundle/View/Strategy/NoneStrategy.php',
	'AssetsBundle\View\Strategy\StrategyInterface' => __DIR__ . '/src/AssetsBundle/View/Strategy/StrategyInterface.php',
	'AssetsBundle\View\Strategy\ViewHelperStrategy' => __DIR__ . '/src/AssetsBundle/View/Strategy/ViewHelperStrategy.php'
);