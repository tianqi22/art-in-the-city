<?php
add_plugin_hook('install', 'json_output_install');

add_filter('define_response_contexts', 'json_output_response_context');
add_filter('define_action_contexts', 'json_output_action_context');

function json_output() {}

function json_output_action_context($context, $controller)
{
    if ($controller instanceof ItemsController) {
        $context['browse'][] = 'jsonp';
		$context['tags'][] = 'jsonp';
    }
    
    return $context;
}

function json_output_response_context($context)
{
    $context['jsonp'] = array('suffix'  => 'jsonp', 
                            'headers' => array('Content-Type' => 'application/json'));
    
    return $context;
}