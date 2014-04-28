<?php

//Router module config
return array(
    'routes' => array(
        'jscustom' => array(
            'type' => 'literal',
            'options' => array('route' => '/jscustom'),
            'may_terminate' => true,
            'child_routes' => array(
                'definition' => array(
                    'type' => 'Zend\Mvc\Router\Http\Segment',
                    'options' => array(
                        'route' => '/:controller/:js_action',
                        'contraints' => array('controller' => '[a-zA-Z][a-zA-Z0-9_-]*', 'js_action' => '[a-zA-Z][a-zA-Z0-9_-]*'),
                        'defaults' => array('action' => 'jscustom')
                    )
                )
            )
        )
    )
);
