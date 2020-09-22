<?php
/**
* Plugin Name: Official Hyperaudio Plugin
* Plugin URI: https://hyper.audio
* Description: Hyperaudio Interactive Transcript Player
* Version: 1.0
* Author: Mark Boas
* Author URI: http://hyper.audio
**/

$globalid = 0;

// scripts to go in the header and/or footer
add_action('init', 'hyperaudio_init');
add_shortcode('hyperaudio', 'hyperaudio_shortcode_handler');

function hyperaudio_shortcode_handler($atts, $content, $tag)
{
  $o = '';

  // defaults
  $src = '';

  if (isset($atts['src'])) $src = esc_html__($atts['src']);
  $transcript = esc_html__($content);

  global $globalid;
  $globalid++;
  $id = $globalid;

  $o .='<p>
    <form id="searchForm" style="float:right">
      Playback Rate <span id="currentPbr">1</span><input id="pbr" type="range" value="1" min="0.5" max="3" step="0.1" style="width:10%">
      <input id="search" type="text" ><input type="submit" value="search">
    </form>
  </p>
  
  <video id="hyperplayer" style="z-index: 5000000; position:relative; width:400px" src="'.src.'" type="audio/mp4" controls></video>

  
  <div id="hypertranscript" style="overflow-y:scroll; height:600px; position:relative; border-style:dashed; border-width: 1px; border-color:#999; padding: 8px">
    <article>'.$content.'</article>
  </div>
  
  ';


  $o .= '<script></script>';

  return $o;
}

function hyperaudio_init()
{
    //global $miniAudioPlayer_version;
    //load_plugin_textdomain('mbMiniAudioPlayer', false, basename(dirname(__FILE__)) . '/languages/');
    //if (!is_admin()) {
        //wp_enqueue_script('jquery');
        wp_enqueue_script('jplayer', plugins_url('/js/hyperaudio-lite.js', __FILE__), false, '1.0.0', false);
        wp_enqueue_script('jplayer', plugins_url('/js/hyperaudio-lite-wrapper.js', __FILE__), false, '1.0.0', false);
        wp_enqueue_script('jplayer', plugins_url('/js/share-this.js', __FILE__), false, '1.0.0', false);
        wp_enqueue_script('jplayer', plugins_url('/js/share-this-twitter.js', __FILE__), false, '1.0.0', false);
    //}
}

add_filter('plugin_action_links', 'hyperaudio_action_links', 10, 2);
function hyperaudio_action_links($links, $file)
{
    static $this_plugin;
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    // check to make sure we are on the correct plugin
    if ($file == $this_plugin) {
        // the anchor tag and href to the URL we want. For a "Settings" link, this needs to be the url of your settings page
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=wp-hyperaudio/hyperaudio-admin.php">Settings</a>';
        // add the link to the list
        array_unshift($links, $settings_link);
    }
    return $links;
}

if (is_admin()) {
  require('hyperaudio-admin.php');
}
