<?php

//Router module config
return array(
    'routes' => array(
        \AssetsBundle\Mvc\Controller\AbstractActionController::JS_CUSTOM_ACTION => array(
            'type' => 'literal',
            'options' => array('route' => '/' . \AssetsBundle\Mvc\Controller\AbstractActionController::JS_CUSTOM_ACTION),
            'may_terminate' => true,
            'child_routes' => array(
                'definition' => array(
                    'type' => 'Zend\Mvc\Router\Http\Segment',
                    'options' => array(
                        'route' => '/:controller/:js_action',
                        'contraints' => array('controller' => '[a-zA-Z][a-zA-Z0-9_-]*', 'js_action' => '[a-zA-Z][a-zA-Z0-9_-]*'),
                        'defaults' => array('action' => \AssetsBundle\Mvc\Controller\AbstractActionController::JS_CUSTOM_ACTION)
                    )
                )
            )
        )
    )
);
