'use strict';

var caption = (function () {

  var cap = {},
    idealSubLength = 40,
    maxSubLength = 50,
    minSubLength = 25,
    words;

 

  function formatSeconds(seconds) {
    return new Date(seconds.toFixed(3) * 1000).toISOString().substr(11, 12);
  }

  cap.init = function(transcriptId) {
    var transcript = document.getElementById(transcriptId);
    var words = transcript.querySelectorAll('[data-m]');

    console.log("CAPTION WORDS");
    console.log(words);

    var data = {};
    data.segments = [];
    var segmentIndex = 0;

    function segmentMeta(speaker, start, duration, chars) {
      this.speaker = speaker;
      this.start = start;
      this.duration = duration;
      this.chars = chars;
      this.words = [];
    }

    function wordMeta(start, duration, text) {
      this.start = start;
      this.duration = duration;
      this.text = text;
    }

    var thisWordMeta;
    var thisSegmentMeta = null;


    //var shouldSplit = false;

    var idealLineLength = 30;
    var maxLineLength = 37;
    var minLineLength = 21;
    //var midLineLength = 16;
    var minLineLength = 11;
    var minDisplayTime = 700;
    var significantPauseTime = 750;
    var significantPause = false;
    var captionNumber = 1;
    var expressSpeakers = true;
    var captionsVtt = "WEBVTT\n"
    var timecode = "";
    var newCaption = true;
    var pauseText = "...";

    var endSentenceDelimiter = /[\.。?؟!]/g;
    var midSentenceDelimiter = /[,、，،و:，…‥]/g;

    // splt into sentences

    words.forEach(function(word, i) {

      if (word.classList.contains("speaker")) {
        thisSegmentMeta = new segmentMeta(word.innerText, null, 0, 0, 0);
        data.segments.push(thisSegmentMeta);
        console.log("speaker - pushing new segment");
      } else {
        if (word.getAttribute("data-d") !== null && word.getAttribute("data-d") !== "0") {

          if (thisSegmentMeta === null) {
            thisSegmentMeta = new segmentMeta("", null, 0, 0, 0);
            console.log("no speaker change - pushing new segment");
            data.segments.push(thisSegmentMeta);
          } 

          var thisStart = parseInt(word.getAttribute("data-m"))/1000;
          var thisDuration = parseInt(word.getAttribute("data-d"))/1000;
          var thisText = word.innerText;

          thisWordMeta = new wordMeta(thisStart, thisDuration, thisText);


          if (data.segments[segmentIndex].start === null) {
            console.log("setting segment data");
            data.segments[segmentIndex].start = thisStart;
            data.segments[segmentIndex].duration = 0;
            data.segments[segmentIndex].chars = 0;
          }

          data.segments[segmentIndex].duration += thisDuration;
          data.segments[segmentIndex].chars += thisText.length;

          data.segments[segmentIndex].words.push(thisWordMeta);

          // remove spaces first just in case
          var lastChar = thisText.replace(/\s/g, '').slice(-1);
          if (lastChar.match(endSentenceDelimiter)) {
            segmentIndex++;
            console.log("NEW SEGMENT");
            console.log(segmentIndex);
            thisSegmentMeta = null;
          }
        }
      }

      
      //console.log(el);
      //console.log(i);
      
    });

    console.log(data);


    data.segments.map(function(segment) {
      if (segment.chars < maxLineLength) {
        captionsVtt += "\n" + formatSeconds(segment.start) + "-->" + formatSeconds(segment.start + segment.duration) + "\n";
        segment.words.forEach(function(wordMeta) {
          captionsVtt += wordMeta.text;
        });
        captionsVtt += "\n";
      } else {
        var charCount = 0;
        var lineText = "";
        var firstLine = true;
        var lastOutTime;
        var lastInTime = null;
        
  
        segment.words.forEach(function(wordMeta) {

          var lastChar = wordMeta.text.replace(/\s/g, '').slice(-1);

          if (lastInTime === null) {
            lastInTime = wordMeta.start;
          }

          if (charCount + wordMeta.text.length > minLineLength && lastChar.match(midSentenceDelimiter)) {

            if (firstLine === true) {
              captionsVtt += "\n" + formatSeconds(lastInTime) + " --> " + formatSeconds(wordMeta.start + wordMeta.duration) + "\n";
            }

            lineText += wordMeta.text;

            captionsVtt += lineText + "\n";

            charCount = 0;
            lineText = "";
            firstLine = !firstLine;
            lastInTime = null;

            console.log(lineText);

          } else {

            if (charCount + wordMeta.text.length > maxLineLength) {
              if (firstLine === true) {
                console.log(lastInTime);
                console.log(lastOutTime);
                captionsVtt += "\n" + formatSeconds(lastInTime) + " --> " + formatSeconds(lastOutTime) + "\n";
              }

              captionsVtt += lineText + "\n";

              charCount = wordMeta.text.length;
              lineText = wordMeta.text;
              firstLine = !firstLine;
              lastInTime = wordMeta.start;
            } else {
              charCount += wordMeta.text.length;
              lineText += wordMeta.text;
            }

            console.log(lineText);
          }
        
          console.log("setting lastOutTime");
          lastOutTime = wordMeta.start + wordMeta.duration;
          console.log(lastOutTime);
        });

        if (firstLine === true) {
          captionsVtt += "\n" + formatSeconds(lastInTime) + " --> " + formatSeconds(lastOutTime) + "\n";
        }
        captionsVtt += lineText + "\n";
        
      }
    });

    document.getElementById('vtt').setAttribute("src", 'data:text/vtt,'+encodeURIComponent(captionsVtt));

    console.log(captionsVtt);

  }

  return cap;

});