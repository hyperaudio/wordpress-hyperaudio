<?php
/**
* Plugin Name: Official Hyperaudio Plugin
* Plugin URI: https://hyper.audio
* Description: Hyperaudio Interactive Transcript Player
* Version: 1.0.11
* Author: Mark Boas
* Author URI: http://hyper.audio
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
 
  if (isset($atts['src'])) $src = esc_html__($atts['src']);
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


  $transcript = preg_replace( "/\r|\n/", "", $transcript);

  $transcript = str_replace("<br />", "", $transcript);

  global $globalid;
  $globalid++;
  $id = $globalid;

  if (is_null($transcriptid)) {
    $transcriptid = "hypertranscript".$id;
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
  }';
  /* FIXME:was not in hyperaudio-lite 2.1.3 -- should this be in the above?
   *   removed out of output so as not to clutter.
  .hyperaudio-transcript a {
    text-decoration:none !important;

  } */

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


  $o .=' 
  .share-this-popover{max-width:8em;pointer-events:none;-webkit-filter:drop-shadow(0 1px 3px rgba(0,0,0,.35));filter:drop-shadow(0 1px 3px rgba(0,0,0,.35));-webkit-animation:a .3s;animation:a .3s}.share-this-popover:before{content:"";position:absolute;bottom:100%;left:50%;width:0;height:0;margin:.25em -.5em;border-width:.5em .5em 0;border-style:solid;border-color:#333 transparent}.share-this-popover>ul{pointer-events:auto;list-style:none;padding:0;margin:-.75em 0 0;white-space:nowrap;background:#333;color:#fff;border-radius:.25em;position:absolute;left:50%;-webkit-transform:translate(-50%,-100%);-ms-transform:translate(-50%,-100%);transform:translate(-50%,-100%)}.share-this-popover>ul>li{display:inline-block;width:2em;height:2em;line-height:2em;text-align:center}.share-this-popover>ul>li>a{display:inline-block;width:100%;height:100%;color:inherit;box-sizing:border-box;padding:.35em}.share-this-popover>ul>li>a:focus,.share-this-popover>ul>li>a:hover{background:hsla(0,0%,100%,.25)}@media (pointer:coarse){.share-this-popover{font-size:150%}.share-this-popover:before{bottom:auto;top:100%;border-width:0 .5em .5em;margin-top:0}.share-this-popover>ul{top:100%;transform:translateX(-50%);margin:.5em 0 0}}@media (max-width:575px){.share-this-popover{left:0!important;right:0!important;width:auto!important;max-width:none}.share-this-popover:before{bottom:auto;top:100%;border-width:0 .5em .5em;margin-top:0}.share-this-popover>ul{top:100%;transform:translateX(-50%);margin:.5em 0 0;left:0;width:100%;transform:none;border-radius:0;text-align:center}}@-webkit-keyframes a{0%{-webkit-transform:translateY(-3em);opacity:0}80%{-webkit-transform:translateY(.5em);opacity:1}to{-webkit-transform:translateY(0)}}@keyframes a{0%{transform:translateY(-3em);opacity:0}80%{transform:translateY(.5em);opacity:1}to{transform:translateY(0)}}</style>';

  $o .='<!--<p>
    <form id="searchForm" style="float:right">
      Playback Rate <span id="currentPbr">1</span><input id="pbr" type="range" value="1" min="0.5" max="3" step="0.1" style="width:10%">
      <input id="search" type="text" ><input type="submit" value="search">
    </form>
  </p>-->';

  $o .='<div id="video-holder">';
  
  if (strpos(strtolower($src), 'youtube.com') !== false || strpos(strtolower($src), 'youtu.be') !== false) {
    if (isset($atts['media-height'])) {
      $o .= '<div><iframe id="hyperplayer'.$id.'" class="hyperaudio-player" width="'.$width.'" height="'.$mediaHeight.'" data-player-type="youtube" frameborder="no" allow="autoplay" src="'.$src.'?enablejsapi=1"></iframe></div>';
    } else {
      $o .= '<div class="iframe-container"><iframe id="hyperplayer'.$id.'" class="hyperaudio-player iframe-video" width="'.$width.'" data-player-type="youtube" frameborder="no" allow="autoplay" src="'.$src.'?enablejsapi=1"></iframe></div>';
    }
  } elseif (strpos(strtolower($src), 'vimeo.com') !== false) {
    $o .= '<iframe id="hyperplayer'.$id.'" class="hyperaudio-player" data-player-type="vimeo" src="'.$src.'" width="'.$width.'" height="'.$height.'" frameborder="no" allowfullscreen allow="autoplay; encrypted-media"></iframe><script src="https://player.vimeo.com/api/player.js"></script>';
  } elseif (strpos(strtolower($src), 'soundcloud.com') !== false) {
    $o .= '<iframe id="hyperplayer'.$id.'" class="hyperaudio-player" data-player-type="soundcloud" scrolling="no" frameborder="no" allow="autoplay" src="'.$src.'" style="width: '.$width.'"></iframe><script src="https://w.soundcloud.com/player/api.js"></script>';
  } elseif (strpos(strtolower($src), '.mp3') !== false) {
    $o .= '<audio id="hyperplayer'.$id.'" class="hyperaudio-player" style="position:relative; width:'.$width.'" src="'.$src.'" controls></audio>';
  } else {
    $o .= '<video id="hyperplayer'.$id.'" class="hyperaudio-player" style="position:relative; width:'.$width.'" src="'.$src.'" controls>';
    
    if ($captionsOn == true) {
      $o .= '<track id="hyperplayer'.$id.'-vtt" label="'.$trackLabel.'" kind="subtitles" srclang="'.$language.'" src="" default>';
    }

    $o .= '</video>';
  }

  $o .='</div>';

 $o .='<div id="'.$transcriptid.'" class="hyperaudio-transcript" style="overflow-y:scroll; width:'.$width.'; height:'.$transcriptHeight.'; position:relative; border-style:dashed; border-width: 1px; border-color:#999; padding: 8px">'.$transcript.'</div><div style="text-align:right; font-size:65%; margin-top: -16px; line-height: 1.0; font-weight: 600; font-family: Work Sans, Helvetica, Arial, sans-serif;"><a href="https://hyper.audio">A Hyperaudio Production</a></div>';


  $o .= '<script>
  ShareThis({
      sharers: [ ShareThisViaTwitter, ShareThisViaClipboard ],
      selector: "article"
  }).init();

  var minimizedMode = false;
  var autoScroll = true;
  var doubleClick = false;

  new HyperaudioLite("'.$transcriptid.'", "hyperplayer'.$id.'", minimizedMode, autoScroll, doubleClick, '.$webmonetization.');';

if ($captionsOn == true) {
  $o .= 'var cap1 = caption();
  cap1.init("'.$transcriptid.'", "hyperplayer'.$id.'", '.$captionMaxLength.' , '.$captionMinLength.');';
}
  
$o .= '  </script>
';

  return $o;
}

function hyperaudio_init()
{
  if (!is_admin()) {
    wp_enqueue_script('velocity', plugins_url('/js/velocity.js', __FILE__), false, '1.0.0', false);
    wp_enqueue_script('hyperaudio-lite', plugins_url('/js/hyperaudio-lite.js', __FILE__), false, '1.0.0', false);
    wp_enqueue_script('caption', plugins_url('/js/caption.js', __FILE__), false, '1.0.0', false);
    wp_enqueue_script('share-this', plugins_url('/js/share-this.js', __FILE__), false, '1.0.0', false);
    wp_enqueue_script('share-this-twitter', plugins_url('/js/share-this-twitter.js', __FILE__), false, '1.0.0', false);
    wp_enqueue_script('share-this-clipboard', plugins_url('/js/share-this-clipboard.js', __FILE__), false, '1.0.0', false);
    wp_enqueue_script('twitter-widget', plugins_url('https://platform.twitter.com/widgets.js', __FILE__), false, '1.0.0', false);
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
    $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=wp-hyperaudio/hyperaudio-admin.php">Settings</a>';
    // add the link to the list
    array_unshift($links, $settings_link);
  }
  return $links;
}

if (is_admin()) {
  require('hyperaudio-admin.php');
}
