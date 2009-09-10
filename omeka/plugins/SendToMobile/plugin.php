<?php
add_plugin_hook('install', 'send_to_mobile_install');
add_plugin_hook('public_append_to_items_show', 'display_send_to_mobile_link');

function send_to_mobile_install() {}

function display_send_to_mobile_link() 
{
	echo "<a href='/send-to-mobile/'><img src='" . img('iphone_small_icon.png') . "' alt='cell phone icon'> Send to Mobile Device</a>";
}