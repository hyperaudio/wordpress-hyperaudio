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

  cap.init = function(transcriptId, playerId, maxLength, minLength) {
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
    var midSentenceDelimiter = /[,、–，،و:，…‥]/g;

    if (!isNaN(maxLength)) {
      maxLineLength = maxLength;
    }

    if (!isNaN(minLength)) {
      minLineLength = minLength;
    }

    // split into sentences

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

    function captionMeta(start, stop, text) {
      this.start = start;
      this.stop = stop;
      this.text = text;
    }

    var captions = [];

    var thisCaption = null;

    data.segments.map(function(segment) {

      if (segment.chars < maxLineLength) {
        thisCaption = new captionMeta(formatSeconds(segment.start), formatSeconds(segment.start + segment.duration), "");
        
        segment.words.forEach(function(wordMeta) {
          thisCaption.text += wordMeta.text;
        });

        thisCaption.text += "\n";

        captions.push(thisCaption);

      } else {

        var charCount = 0;
        var lineText = "";
        var firstLine = true;
        var lastOutTime;
        var lastInTime = null;
        
        

        console.log("segment.chars");
        console.log(segment.chars);
        console.log(segment);
        
        segment.words.forEach(function(wordMeta, index) {

          var lastChar = wordMeta.text.replace(/\s/g, '').slice(-1);

          if (lastInTime === null) {
            lastInTime = wordMeta.start;
          }

          if (charCount + wordMeta.text.length > minLineLength && lastChar.match(midSentenceDelimiter)) {

            if (firstLine === true) {

              thisCaption = new captionMeta(formatSeconds(lastInTime), formatSeconds(wordMeta.start + wordMeta.duration), "");
              thisCaption.text += lineText + wordMeta.text + "\n";
              
              //check for last word in segment

              if (index + 1 >= segment.words.length) {
                captions.push(thisCaption);
              } else {
                firstLine = false;
              }

            } else {

              thisCaption.stop = formatSeconds(wordMeta.start + wordMeta.duration);
              thisCaption.text += lineText + wordMeta.text + "\n";
              captions.push(thisCaption);
              thisCaption = null;
              firstLine = true;
            }

            charCount = 0;
            lineText = "";
            lastInTime = null;

          } else {

            if (charCount + wordMeta.text.length > maxLineLength) {

              if (firstLine === true) {
                console.log(lastInTime);
                console.log(lastOutTime);
                thisCaption = new captionMeta(formatSeconds(lastInTime), formatSeconds(lastOutTime), "");
                thisCaption.text += lineText + "\n";

                if (index >= segment.words.length) {
                  captions.push(thisCaption);
                  thisCaption = null;
                } else {
                  firstLine = false;
                }

              } else {

                thisCaption.stop = formatSeconds(lastOutTime);
                thisCaption.text += lineText + "\n";
                captions.push(thisCaption);
                thisCaption = null;
                firstLine = true;
              }

              charCount = wordMeta.text.length;
              lineText = wordMeta.text;
              lastInTime = wordMeta.start;

            } else {

              charCount += wordMeta.text.length;
              lineText += wordMeta.text;
            }
            
          }

          //console.log(lineText);

          lastOutTime = wordMeta.start + wordMeta.duration;
        });

        console.log(lineText);
        
        if (thisCaption != null) {
          thisCaption.stop = formatSeconds(lastOutTime);
          thisCaption.text += lineText + "\n";
          captions.push(thisCaption);
        } else {
          thisCaption = new captionMeta(formatSeconds(lastInTime), formatSeconds(lastOutTime), lineText);
          captions.push(thisCaption);
        }

      }
    });

    console.log(captions);

    captions.forEach(function(caption) {
      captionsVtt += "\n" + caption.start + "-->" + caption.stop + "\n" + caption.text + "\n";
    });

    document.getElementById(playerId+'-vtt').setAttribute("src", 'data:text/vtt,'+encodeURIComponent(captionsVtt));

  }

  return cap;

});