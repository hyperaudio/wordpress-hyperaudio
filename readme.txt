=== Hyperaudio ===
Contributors: Maboas
Donate link: https://patreon.com/hyperaudio
Tags: Podcasts, Captions, Transcripts, Interactive Transcripts, Accessibility, Media, Audio, Video, Subtitles
Requires at least: 3.1
Tested up to: 6.3
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Associate interactive transcripts with your audiovisual content and make your media more accessible to people and search engines.

== Description ==

Allows you to import and convert data from a number of popular speech-to-text providers and tools. 

The result is an interactive transcript where words "light up" as they are spoken. 

Clicking on words in a transcript takes you directly to equivalent part of your media, while selecting a passage of text creates a handy link back to the highlighted part – great for sharing!

Currently the transcript generator (provided in the settings) can convert from the following speech-to-text outputs:

* [OpenEditor](https://github.com/OpenEditor/openeditor/) JSON
* [Speechmatics](https://speechmatics.com) JSON
* [Google Speech-to-text](https://cloud.google.com/speech-to-text/) JSON
* [Gentle](http://lowerquality.com/gentle/) JSON
* [.srt formatted](https://en.wikipedia.org/wiki/SubRip) captions

Two views of the resultant transcript are provided...

= Hypertranscript View =

A Hypertranscript is an [HTML](https://developer.mozilla.org/en-US/docs/Web/HTML) representation of a media file's spoken audio. This format is ready to be pasted between the Hyperaudio shortcode. Note that since Hypertranscripts create timings and HTML is a format that is both human and machine readable, timings and words can be tweaked by editing this HTML.

= Rendered View =

The rendered view is how the text itself will look once viewed as an Interactive Transcript. Words can be edited from the rendered view but timings of words may not be maintained if you (say) replace two words with one, or paste content into the text.

Once you are happy with your text you need to locate your source media and you're ready to create an Interactive Transcript within your post using the `[hyperaudio]` shortcode.

Something like this :

``[hyperaudio src="link-to-media.mp4"]

``<article>
`` <section>
``  <p>
``   <span data-m="1390" data-d="200">This </span>
``   <span data-m="2510" data-d="700">interactive </span>
``   <span data-m="3220" data-d="550">transcript </span>
``   <span data-m="3820" data-d="200">is </span>
``   <span data-m="4340" data-d="320">great! </span>
`` </p> 
`` </section>
``</article>

``[/hyperaudio]

You can specify a number of parameters including player type. So far we support the following players:

* YouTube
* SoundCloud
* Vimeo
* Videojs

As well as native mp4 and mp3 files.

= Captions =

The module also creates captions for your videos. Useful when viewing content in fullscreen.

= Accessibility =

All in all we make audiovisual media more accessible. 

Accessible to those with hearing difficulties or people consuming content which is not necessarily in their first language. 

Accessible to those choosing to view content with the audio off. Accessible to search engines so that content can be indexed and more easily discovered.

= Flexibility =

You are not limited to creating Interactive Transcripts. You can format the HTML any way you want. Hyperaudio can be used to create "chapter points". Some have even used it to define songs and artists within a musical mix.

= Explainer Videos =

* [How to use the Hyperaudio Wordpress Interactive Transcript – Part 1](https://youtu.be/3Qpq8kj4PxM)

* [How to use the Hyperaudio Wordpress Interactive Transcript – Part 2](https://youtu.be/vIXHCYYSFM0)

* [How to use the Hyperaudio Wordpress Interactive Transcript – Part 3](https://youtu.be/ly08N9S1ZlE)

* [Web Monetization in the Hyperaudio Wordpress Interactive Transcript](https://youtu.be/8kRNh8iBkVk)

== Screenshots ==

native_audio.png
native_video.png
selecting_shortcode.png
sharing_excerpt.png
shortcode_usage.png
soundcloud_player.png
transcript_converter.png
transcript_converter_howto.png
transcript_converter_howto_customize.png
transcript_converter_interface.png
transcript_converter_markup.png
upload_media.png


== Frequently Asked Questions ==

= How do I make my own Interactive Transcript? =

Probably the easiest way is to use the [Hyperaudio Lite Editor](https://hyperaudio.github.io/hyperaudio-lite-editor/).

Alternatively you can use the [OpenEditor](https://github.com/OpenEditor/openeditor/) (transcript editor) which you can find on GitHub.

Or you can use Google Speech-to-Text Service, Speechmatics or Gentle's aligner (especially if you already have the transcript and just want to add timings).

You can also convert captions or subtitles in `.srt` format to timed transcripts, although exact word timing cannot be ensured in that case.

= Can I change the transcript's content? =

Yes. The transcript is editable. Changing one word will generally maintain that word's timing, replacing more than one word may result in more than one word with the same timing.

Note – as transcripts are represented as HTML, you can edit the HTML directly should you need to refine text or styling.

= I'd like to contribute. Where is the repository? =

Our code is open source and we're always looking for help. You can find the code and discussion at [github.com/hyperaudio/wordpress-hyperaudio](https://github.com/hyperaudio/wordpress-hyperaudio/).