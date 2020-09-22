<?php

add_action('admin_menu', 'fabrica_add_option_page');

function hyperaudio_add_option_page()
{
	// hook in the options page function
	add_options_page('Official Hyperaudio Plugin', 'hyperaudio', 'manage_options',  __FILE__, 'hyperaudio_options_page');
}

function hyperaudio_options_page() 
{// Output the options page
  ?>
  <h1>Official Wordpress Plugin - How To Use</h1>
<?php } ?>


