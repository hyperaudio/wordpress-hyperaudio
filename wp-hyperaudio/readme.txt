=== Hyperaudio ===
Contributors: Maboas
Donate link: https://patreon.com/hyperaudio
Tags: Transcripts, Interactive Transcripts, Accessibility, Media, Audio, Video, Subtitles, Captions
Requires at least: 3.1
Tested up to: 6.3
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Associate interactive transcripts with your audiovisual content and make your media more accessible to people and search engines.

== Description ==

The Hyperaudio Wordpress Plugin allows you to import and convert data from a number of popular speech-to-text providers and tools. The result is an interactive transcript where words "light up" as they are spoken. Clicking on words in a transcript takes you directly to equivalent part of your media, while selecting a passage of text creates a handy link back to the highlighted part – great for sharing!

Currently the transcript generator (provided in the settings) can convert from the following speech-to-text outputs:

* OpenEditor JSON
* Speechmatics JSON
* Google Speech-to-text JSON
* LowerQuality Gentle
* SRT formatted captions

Two views of the resultant transcript are provided:

== Hypertranscript View ==

A Hypertranscript is an HTML representation of a media file's spoken audio. This format is ready to be pasted between the Hyperaudio shortcode. Note that since Hypertranscripts create timings and HTML is a format that is both human and machine readable, timings and words can be tweaked by editing this HTML.

== Rendered View ==

The rendered view is how the text itself will look once viewed as an Interactive Transcript. Words can be edited from the rendered view but timings of words may not be maintained if you (say) replace two words with one, or paste content into the text.

Once you are happy with your text you need to locate your source media and you're ready to create an Interactive Transcript within your post using the `[hyperaudio]` shortcode.

Something like this :

``[hyperaudio src="link-to-media.mp4"]

``<article>
`` <section>
``  <p>
``   <span data-m="1390" data-d="200">This </span>
``   <span data-m="2510" data-d="700">wordpress </span>
``   <span data-m="3220" data-d="550">plugin </span>
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

== Captions ==

The plugin also creates captions for your videos. Useful when viewing content in fullscreen.

== Accessibility ==

All in all the Hyperaudio Wordpress plugin makes audiovisual media more accessible. Accessible to those with hearing diffuculties or people consuming content which is not neccessarily in their first language. Accessible to those choosing to view content with the audio off. Accessible to search engines so that content can be indexed and more easily discovered.

== Frequently Asked Questions

= How do I produce the transcript data? =

Probably the easiest way is to use the Hyperaudio Lite Editor.

Alternatively you can use the OpenEditor (transcript editor) which you can find on GitHub.

Or you can use Google Speech-to-Text Service, Speechmatics or Gentle's aligner (especially if you already have the transcript and just want to add timings).

You can also convert captions or subtitles in SRT format to timed transcripts, although exact word timing cannot be ensured in that case.

= Can I change the transcript from within the plugin? =

Yes. The transcript is editable. Changing one word will generally maintain that word's timing, replacing more than one word may result in more than one word with the same timing.

Note – as transcripts are represented as HTML, you can edit the HTML directly should you need to refine text or styling.