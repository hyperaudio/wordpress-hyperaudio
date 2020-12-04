<?php

add_action('admin_menu', 'hyperaudio_add_option_page');

function hyperaudio_add_option_page()
{
	// hook in the options page function
	add_options_page('Official Hyperaudio Plugin', 'hyperaudio', 'manage_options',  __FILE__, 'hyperaudio_options_page');
}


function hyperaudio_load_admin_script($hook)
{
	if ($hook != 'settings_page_wp-hyperaudio/hyperaudio-admin') {
		return;
	}
  //wp_enqueue_script('converter', plugins_url('/js/converter.js', __FILE__), false, '1.0.0', false);
  
  wp_enqueue_script( 'converter', plugin_dir_url( __FILE__ ) . '/js/converter.js', array( 'jquery' ), '1.0.0', true );

}

add_action('admin_enqueue_scripts', 'hyperaudio_load_admin_script');

function hyperaudio_options_page() 
{// Output the options page
  ?>
  <h1>Official Wordpress Plugin - How To Use</h1>

  <link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
  <style type="text/css">

    .box {
		  color: #000;
		  font-size: 150%;
      background-color: #e0e0e0;
      margin: 0;
      padding: 0;
	  }

    .holder {
      display: grid;
		  grid-template-rows: repeat(2, [row] auto  );
		  background-color: #e0e0e0;
		  color: #000;
      display: inline-grid;
      padding: 20px;
    }

    .instructions-convert {
      grid-column: col / span 2;
      grid-row: row;
      line-height: 150%;
      padding-right: 40px;
      /*max-width: 40ch;*/
	  }

	  .instructions-format {
      grid-column: col 3 / span 2;
      grid-row: row;
      line-height: 150%;
	  }

    .source-title {
      grid-column: col / span 2;
      grid-row: row 2;
	  }

	  .target-title {
      grid-column: col 3 / span 2;
      grid-row: row 2;
	  }

	  .source-content {
      grid-column: col / span 2;
      grid-row: row 3;
	  }

	  .target-content {
      grid-column: col 3 / span 2;
      grid-row: row 3;
	  }

    .entry-content {
      resize: both;
      outline: 2px solid #fff;
      box-shadow: 0px;
    }

    .tab {
      border: solid;
      border-width:0px;
      border-bottom:thick dotted #ffffff;
      text-decoration: none;
      padding: 4px 4px 8px 4px;
      font-size: 16px;
      background-color: #fff;
    }

    a.inactive {
      border: none;
      text-decoration:underline;
      background-color: #e0e0e0;
    }

    a {
      color: #2c3e50;
    }

    #transform {
      transition: box-shadow .3s;
      margin-top: 20px;
      font-size: 24px;
      font-weight: bold;
      /*margin-left: 310px;*/
      width: 100%;
      border: solid;
      border-width:2px;
      padding: 8px;
      cursor: pointer;
    }

    #transform:hover {
      box-shadow: 0 0 11px rgba(33,33,33,.2); 
    }

    textarea {
      border: 0;
    }
    
    #rtranscript {
      padding: 8px;
      font-size: 16px;
      line-height: 20px;
      overflow: scroll;
      overflow-x: hidden;
      height: 770px;
      background-color: #fff;
    }

  </style>
</head>
<body>
<div id="wrapper">
  
  <div class="plugin-instructions">
    <p>When defining the source of your media using the src attribute in the hyperaudio shortcode you need to use embed versions for YouTube and SoundCloud.</p>
    <p>For example:<p>
    <p><code>https://www.youtube.com/embed/xLcsdc823dg</code></p>
    <p>or</p>
    <p><code>https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/730479133&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true</code> *</p>
    <p>* Grab the snippet of code from the SoundCloud page containing the file you're interested in, clicking on Share and then Embed.</p>
  </div>

  <div class="holder">
    
    <div class="box instructions-convert">
      <h2>Convert various formats into a Hypertranscript...</h2>
      <p>Paste your <a href="http://en.wikipedia.org/wiki/.srt#SubRip_text_file_format">SRT (subtitle) file</a>, <a href="http://speechmatics.com">Speechmatics</a> JSON, <a href="https://lowerquality.com/gentle/">Gentle</a> JSON, <a href="https://cloud.google.com/speech-to-text">Google STT</a> JSON into the pane below and press the <strong>Convert!</strong> button.</p>
      <form>
        <!--<p><input id="line-breaks" type="checkbox" name="linebreaks" value="on"> Line breaks in output?</p>-->
        <p>
        <strong>Input Format :</strong>
        <select id="format-select">
          <option value="oe">OpenEditor JSON</option>
          <option value="srt">SRT formatted captions</option>
          <option value="speechmatics">Speechmatics JSON</option>
          <option value="gentle">Gentle JSON</option>
          <option value="google">Google Speech-to-Text</option>
          <option value="other">Other</option>
        </select>
        </p>
        <p><input id="word-length" type="checkbox" name="wordlength" value="on"> Take word-length into account when calculating word timings from SRT?</p>
      </form>
    </div>

    <div class="box source-title">
      <h3 id="srt-title" class="entry-title">Paste here ⤵</h3>
    </div>

    <div class="box source-content">
      <textarea id="subtitles" class="entry-content" rows="40" cols="54"></textarea>
    </div>

    <div class="box target-title">
      <h3 id="script-title" class="entry-title">hypertranscript appears here ⤵</h3>      
      <a id="markup-view" class="tab" href="#">Markup View</a> <a id="rendered-view" class="tab inactive" href="#">Rendered View</a>
    </div>

    <div class="box instructions-format">
    
      <h2>And format.</h2>
      <form class="controls">
          <p>Paragraph split on delay:</p>
          <p>0 <input id="para-split" style="width:430px" type="range" value="2.0" min="0.0" max="10" step="0.1"> 10<br/>
          <span style="margin-left:200px" id="current-para-split">2.0</span> seconds</p>
          <p><input id="para-punctuation" type="checkbox" name="wordlength" value="on" checked> Only split paras on text finishing with punctuation. (. ! ?)</p>
      </form>
      <p><button id="transform">Convert! </button> </p>
    </div>

    <div class="box target-content">
      <textarea id="htranscript" class="entry-content" rows="40" cols="54"></textarea>
      <div style="display:none" id="rtranscript" contentEditable></div>
    </div>

  </div>

  <div class="footer">
  </div>
</div>
<?php } ?>


