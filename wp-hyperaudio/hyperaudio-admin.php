<?php

add_action('admin_menu', 'hyperaudio_add_option_page');

function hyperaudio_add_option_page()
{
	// hook in the options page function
	add_options_page('Official Hyperaudio Plugin', 'hyperaudio', 'manage_options',  __FILE__, 'hyperaudio_options_page');
}

add_action('admin_enqueue_scripts', 'hyperaudio_load_admin_script');
function hyperaudio_load_admin_script($hook)
{
	if ($hook != 'settings_page_wp-hyperaudio/hyperaudio-admin') {
		return;
	}
	wp_enqueue_script('converter', plugins_url('/js/converter.js', __FILE__), false, '1.0.0', false);

}

function hyperaudio_options_page() 
{// Output the options page
  ?>
  <h1>Official Wordpress Plugin - How To Use</h1>

  <link href='https://fonts.googleapis.com/css?family=Varela+Round' rel='stylesheet' type='text/css'>
  <style type="text/css">
    .wrapper {
      width: 980px;
      background: #ffffff;
      margin: 0 auto;
    }

    .header {
      background: black;
      color: white;
    }

    .holder {
      margin-top: 50px;
      width: 50%;
      float: left;
      background: #ffffff;
    }

    .instructions {
      padding: 10px;
      height: 310px;
      font-size: 16px;
      line-height: 24px;
    }

    .footer {
      height: 10px;
      background: #ffffff;
      clear: both;
    }

    textarea, #rtranscript {
      margin-left:10px;
      padding: 10px;
      outline: 2px dashed #2c3e50;
      border: none;margin-top:10px;
    }

    textarea:hover {
      outline: 2px dashed #2c3e50;
      border: none;
    }

    .tab {
      border: solid;
      border-width:1px;
      border-bottom:thick dotted #ffffff;
      text-decoration: none;
      padding: 4px;
    }

    a.inactive {
      border: none;
      text-decoration:underline;
      padding: 4px;
    }

    a {
      color: #2c3e50;
    }

    #transform {
      font-size: 24px;
      font-weight: bold;
      margin-left: 310px;
      border: solid;
      border-width:2px;
      padding: 8px;
      text-decoration: none;
    }

    .controls {
      padding-bottom: 30px;
    }

    #rtranscript {
      padding: 8px;
      font-size: 16px;
      line-height: 20px;
      overflow:scroll;
      overflow-x:hidden;
      height: 500px;
    }

  </style>
</head>
<body>
<div id="wrapper">
  <header id="header">
    <h1>Hyperaudio Converter</h1>
  </header>

  <div class="holder">
    <div class="instructions">

      <h2>Convert formats into a hypertranscript.</h2>
      <p>Paste your <a href="http://en.wikipedia.org/wiki/.srt#SubRip_text_file_format">SRT (subtitle) file</a>, <a href="http://speechmatics.com">Speechmatics</a> JSON or <a href="https://lowerquality.com/gentle/">Gentle</a> JSON into the pane below and press the transform button.</p>
      <form>
        <!--<p><input id="line-breaks" type="checkbox" name="linebreaks" value="on"> Line breaks in output?</p>-->
        <p>
        Input Format :
        <select id="format-select">
          <option value="srt">SRT formatted captions</option>
          <option value="speechmatics">Speechmatics JSON</option>
          <option value="gentle">Gentle JSON</option>
          <option value="google">Google Speech-to-Text</option>
          <option value="other">Trint</option>
        </select>
        </p>
        <p><input id="word-length" type="checkbox" name="wordlength" value="on"> Take word-length into account when calculating from SRT?</p>
      </form>
      <h3 id="srt-title" class="entry-title">Paste here ⤵</h3>
    </div>

    <div class="source-content">
      <textarea id="subtitles" class="entry-content" rows="40" cols="54"></textarea>
    </div>
  </div>

  <div class="holder">
    <div class="instructions">
        <form class="controls">
            <p style="margin-top:16px">Paragraph split on delay:</p>
            <p>0 <input id="para-split" style="width:430px" type="range" value="0" min="0.0" max="10" step="0.1"> 10<br/>
            <span style="margin-left:200px" id="current-para-split">0</span> seconds</p>
            <p><input id="para-punctuation" type="checkbox" name="wordlength" value="on"> Only split paras on text finishing with punctuation. (. ! ?)</p>
        </form>
        <p><a id="transform" href="#">Transform <img class="transform-spinner" style="display:none" src="/pad/images/ajax-loader-ffffff-on-808080.gif"></a></p>
        <h3 id="script-title" class="entry-title">hypertranscript appears here ⤵</h3>
        <a id="markup-view" class="tab" href="#">Markup View</a> <a id="rendered-view" class="tab inactive" href="#">Rendered View</a>
    </div>
    <div class="target-content">
      <textarea id="htranscript" class="entry-content" rows="40" cols="54"></textarea>
      <div style="display:none" id="rtranscript"></div>
    </div>
  </div>

  <div class="footer">
  </div>
</div>
<?php } ?>


