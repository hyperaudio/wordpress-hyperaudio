<?php
/**
* Plugin Name: Hyperaudio Interactive Transcript
* Plugin URI: https://hyper.audio
* Description: Hyperaudio Interactive Transcript Maker and Player – maximise your audio and video content's accessibility to humans and search engines.
* Version: 1.0.23
* Author: Mark Boas
* Author URI: https://maboa.it 
**/

$globalid = 0;

// scripts to go in the header and/or footer
add_action('init', 'hyperaudio_init');
add_shortcode('hyperaudio', 'hyperaudio_shortcode_handler');

function hyperaudio_shortcode_handler($atts, $transcript, $tag)
{
  $o = '';
  $src = '';

  // defaults
  $width = '100%';
  $height = '100%';
  $transcriptHeight = '600px';
  $mediaHeight = '';
  //$fontfamily = '"Palatino Linotype", "Book Antiqua", Palatino, serif';
  $fontfamily = NULL;
  $transcriptid = NULL;
  $captionMaxLength = 37;
  $captionMinLength = 21;
  $captionsOn = true;
  $language = "en";
  $trackLabel = "English";
  $webmonetization = false;
  $showActive = false;
  $player = NULL;
  $showHyperaudioLink = false;

  if (isset($atts['src'])) $src = $atts['src'];
  if (isset($atts['width'])) $width = $atts['width'];
  if (isset($atts['height'])) $height = $atts['height'];
  if (isset($atts['transcript-height'])) $transcriptHeight = $atts['transcript-height'];
  if (isset($atts['media-height'])) $mediaHeight = $atts['media-height'];
  if (isset($atts['font-family'])) $fontfamily = $atts['font-family'];
  if (isset($atts['id'])) $transcriptid = $atts['id'];

  if (isset($atts['captions'])) $captionsOn = $atts['captions'];
  if (isset($atts['caption-max'])) $captionMaxLength = $atts['caption-max'];
  if (isset($atts['caption-min'])) $captionMinLength = $atts['caption-min'];
  if (isset($atts['language'])) $language = $atts['language'];
  if (isset($atts['track-label'])) $trackLabel = $atts['track-label'];

  if (isset($atts['webmonetization'])) $webmonetization = $atts['webmonetization'];
  if (isset($atts['show-active'])) $showActive = $atts['show-active'];

  if (isset($atts['player'])) $player = $atts['player'];
  if (isset($atts['show-hyperaudio-link'])) $showHyperaudioLink = $atts['show-hyperaudio-link'];


  $transcript = preg_replace( "/\r|\n/", "", $transcript);

  $transcript = str_replace("<br />", "", $transcript);

  global $globalid;
  $globalid++;
  $id = $globalid;

  if (is_null($transcriptid)) {
    $transcriptid = "hypertranscript".$id;
  }

  if (strtolower($player) == 'videojs') {
    $o .='<link href="https://vjs.zencdn.net/8.5.2/video-js.css" rel="stylesheet" />';
  }
  
  $o .='<style>

  .iframe-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
    height: 0;
  }
  .iframe-video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  
  .hyperaudio-transcript header {
    font-size: 200%;
  }
  
  .hyperaudio-transcript a, a.link {
    border: 0px;
  }
  
  .hyperaudio-transcript .read {
    color: #000;
  }
  
  .hyperaudio-transcript .unread {
    color: #777;
  }

  .hyperaudio-transcript .search-match {
    background-color: pink;
  }

  .hyperaudio-transcript .share-match {
    background-color: #66ffad;
  }
  
  .hyperaudio-transcript sub:before {
    content: "\231C";
  }

  .hyperaudio-transcript sub.highlight-duration:before {
    content: "\231D";
  }
  
  .hyperaudio-transcript h5 {
    font-size: 130%;
  }
  
  [data-m] {
    cursor: pointer;
  }
  
  .hyperaudio-transcript {
    line-height: 1.5;
  }
  
  .speaker {
    font-weight: bold;
  }

  .hyperaudio-transcript a {
    text-decoration:none !important;
  }
    
  #popover {
    position: absolute;
    background-color: #f9f9f9;
    
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    display: none;
    z-index: 1;
    font-size: small;
    font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
  }

  #popover a {
    text-decoration: none; 
    color: #303030;
    cursor: pointer;
  }

  #clipboard-text {
    font-family: Courier New,Courier,Lucida Sans Typewriter,Lucida Typewriter,monospace; 
    font-size: 0.8em;
    line-height: 1.2;
  }

  #clipboard-confirm {
    font-size: medium;
  }

  #clipboard-dialog {
    width: 50%;
  }
  ';

  if (!is_null($fontfamily)) {
    $o .=' .hyperaudio-transcript {
      font-family: '.$fontfamily.';
    }';
  }

  if ($showActive == true) {
    $o .=' .hyperaudio-transcript .active {
      background-color: #efefef;
      color: #0000cc;
    }
    .hyperaudio-transcript .active > .active {
      background-color: #ccf;
      text-decoration: #00f underline;
      text-decoration-thickness: 3px;
    }';
  }

  if ($showHyperaudioLink == true) {
    $o .=' .hyperaudio-link {
      text-align:right; 
      font-size:65%; 
      line-height: 1.0; 
      font-weight: 600; 
      font-family: Work Sans, Helvetica, Arial, sans-serif;
    }';
  }


  $o .='</style>';

  $o .='<!--<p>
    <form id="searchForm" style="float:right">
      Playback Rate <span id="currentPbr">1</span><input id="pbr" type="range" value="1" min="0.5" max="3" step="0.1" style="width:10%">
      <input id="search" type="text" ><input type="submit" value="search">
    </form>
  </p>-->';

  $o .='<div id="video-holder">';

  if (strpos(strtolower($src), 'youtube.com') !== false || strpos(strtolower($src), 'youtu.be') !== false || strtolower($player) == 'youtube') {
    if (isset($atts['media-height'])) {
      $o .= '<div><iframe id="hyperplayer'. esc_attr( $id ) .'" class="hyperaudio-player" width="'. esc_attr( $width ) .'" height="'. esc_attr( $mediaHeight ).'" data-player-type="youtube" frameborder="no" allow="autoplay" src="'. esc_url( $src ) .'?enablejsapi=1"></iframe></div>';
    } else {
      $o .= '<div class="iframe-container"><iframe id="hyperplayer'. esc_attr( $id ).'" class="hyperaudio-player iframe-video" width="'. esc_attr( $width ) .'" data-player-type="youtube" frameborder="no" allow="autoplay" src="'. esc_url( $src ) .'?enablejsapi=1"></iframe></div>';
    }
  } elseif (strpos(strtolower($src), 'vimeo.com') !== false || strtolower($player) == 'vimeo') {
    $o .= '<iframe id="hyperplayer'. esc_attr( $id ) .'" class="hyperaudio-player" data-player-type="vimeo" src="'. esc_url( $src ) .'" width="'. esc_attr( $width ) .'" height="'. esc_attr( $height ).'" frameborder="no" allowfullscreen allow="autoplay; encrypted-media"></iframe><script src="https://player.vimeo.com/api/player.js"></script>';
  } elseif (strpos(strtolower($src), 'soundcloud.com') !== false || strtolower($player) == 'soundcloud') {
    $o .= '<iframe id="hyperplayer'. esc_attr( $id ) .'" class="hyperaudio-player" data-player-type="soundcloud" scrolling="no" frameborder="no" allow="autoplay" src="'. esc_url( $src ) .'" style="width: '. esc_attr( $width ) .'"></iframe><script src="https://w.soundcloud.com/player/api.js"></script>';
  } elseif (strtolower($player) == 'videojs') {
    $o .= '<video id="hyperplayer'. esc_attr( $id ) .'" class="hyperaudio-player video-js" data-player-type="videojs" data-setup="{}" style="position:relative" src="'. esc_url( $src ) .'" width="'. esc_attr( $width ) .'" height="'. esc_attr( $mediaHeight ) .'" controls><script src="https://vjs.zencdn.net/8.5.2/video.min.js"></script>';
  } elseif (strtolower($player) == 'spotify') {
    $o .= '<script src="https://open.spotify.com/embed/iframe-api/v1" async></script><div id="hyperplayer'. esc_attr( $id ) .'" data-player-type="spotify" src="'. esc_url( $src ) .'" uri=""></div>';
  } elseif (strpos(strtolower($src), '.mp3') !== false || strtolower($player) == 'nativeaudio') {
    $o .= '<audio id="hyperplayer'. esc_attr( $id ) .'" class="hyperaudio-player" style="position:relative; width:'. esc_attr( $width ).'" src="'. esc_url( $src ) .'" controls></audio>';
  } else {
    $o .= '<video id="hyperplayer'. esc_attr( $id ) .'" class="hyperaudio-player" style="position:relative; width:'. esc_attr( $width ) .'" src="'. esc_url( $src ) .'" controls>';

    if ($captionsOn == true) {
      $o .= '<track id="hyperplayer'. esc_attr( $id ) .'-vtt" label="'. esc_attr( $trackLabel ) .'" kind="subtitles" srclang="'. esc_attr( $language ) .'" src="" default>';
    }

    $o .= '</video>';

    $o .= '<div id="popover"><a id="popover-btn">Copy to clipboard <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="-130 -110 600 600"><path d="m161,152.9h190c0.1,0 0.1,0 0.2,0 10.8,0 19.6-7.1 19.6-16 0-1.5-14.1-82.7-14.1-82.7-1.3-7.9-9.6-13.8-19.4-13.8l-61.7,.1v-13.5c0-8.8-8.8-16-19.6-16-10.8,0-19.6,7.1-19.6,16v13.6l-61.8,.1c-9.8,0-18,5.9-19.4,13.8l-13.7,80.3c-1.2,14.3 13.5,18.1 19.5,18.1z" fill="currentcolor"/><path d="m427.5,78.9h-26.8c0,0 9.3,53.5 9.3,58 0,30.4-26.4,55.2-58.8,55.2h-190.2c-19.6,0.4-63.3-14.7-58.1-63.9l8.4-49.2h-26.8c-10.8,0-19.6,8.8-19.6,19.6v382.9c0,10.8 8.8,19.6 19.6,19.6h343c10.8,0 19.6-8.8 19.6-19.6v-383c0-10.8-8.8-19.6-19.6-19.6zm-76.5,320.1h-190c-10.8,0-19.6-8.8-19.6-19.6 0-10.8 8.8-19.6 19.6-19.6h190c10.8,0 19.6,8.8 19.6,19.6 0,10.8-8.7,19.6-19.6,19.6zm0-110.3h-190c-10.8,0-19.6-8.8-19.6-19.6 0-10.8 8.8-19.6 19.6-19.6h190c10.8,0 19.6,8.8 19.6,19.6 0,10.8-8.7,19.6-19.6,19.6z" fill="currentcolor"/></svg></a></div>

  <dialog id="clipboard-dialog">
      <h4>The following text has been copied to the clipboard</h4>
      <p id=clipboard-text></p>
      <div style="text-align: right;">
        <button id="clipboard-confirm">ok</button>
      </div>
  </dialog>';
  }

  $o .='</div>';

  $hyperaudioLink = '';
  if ($showHyperaudioLink === true) {
    $hyperaudioLink = '<div class="hyperaudio-link" style="text-align:right; font-size:65%; line-height: 1.0; font-weight: 600; font-family: Work Sans, Helvetica, Arial, sans-serif;"><a href="https://hyperaudio.site">A Hyperaudio Production</a></div>';
  }

  $o .='<div id="'. esc_attr( $transcriptid ) .'" class="hyperaudio-transcript" style="overflow-y:scroll; width:'. esc_attr( $width ) .'; height:'. esc_attr( $transcriptHeight ) .'; position:relative; border-style:dashed; border-width: 1px; border-color:#999; padding: 8px">'. wp_kses_post( $transcript ) . wp_kses_post( $hyperaudioLink ) . '</div>';

  $o .= '<script>

  var minimizedMode = false;
  var autoScroll = true;
  var doubleClick = false;

  new HyperaudioLite("'. esc_js( $transcriptid ).'", "hyperplayer'. esc_js( $id ) .'", minimizedMode, autoScroll, doubleClick, '. esc_js( $webmonetization ) .');';

if ($captionsOn == true) {
  $o .= 'var cap1 = caption();
  cap1.init("'. esc_js( $transcriptid ) .'", "hyperplayer'. esc_js( $id ) .'", '. esc_js( $captionMaxLength ) .' , '. esc_js( $captionMinLength ) .');';
}
  
$o .= '  </script>
';

  return $o;
}

function hyperaudio_init()
{
  if (!is_admin()) {
    wp_enqueue_script('velocity', plugins_url('/js/velocity.js', __FILE__), array(), '1.0.0', false);
    wp_enqueue_script('hyperaudio-lite', plugins_url('/js/hyperaudio-lite.js', __FILE__), array(), '1.0.0', false);
    wp_enqueue_script('caption', plugins_url('/js/caption.js', __FILE__), array(), '1.0.0', false);
  }
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
    $settings_link = '<a href="' . esc_url( get_bloginfo('wpurl') ) . '/wp-admin/options-general.php?page=wp-hyperaudio/hyperaudio-admin.php">Settings</a>';
    // add the link to the list
    array_unshift($links, $settings_link);
  }
  return $links;
}

if (is_admin()) {
  require('hyperaudio-admin.php');
}
