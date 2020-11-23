'use strict';

var caption = (function () {

  var cap = {},
    idealSubLength = 40,
    maxSubLength = 50,
    minSubLength = 25,
    words;

  function createCaptions(data) {

    console.log(data);

      var vttOut = 'WEBVTT';
      var srtOut = '';
      var srtIndex = 1;
      var ttmlOut = '<?xml version="1.0" encoding="UTF-8"?>\n'
                  + '<tt xmlns="http://www.w3.org/ns/ttml">\n'
                  + '<head></head>\n';
                  + '<body><div>\n';
  
      var endSentenceDelimiter = /[\.。?؟!]/g;
      var midSentenceDelimiter = /[,、，،و:，…‥]/g;
  
      data.segments.map(function(segment) {
  
        console.log(minSubLength);
        console.log(idealSubLength);
        console.log(maxSubLength);
  
        var letterDuration = (segment.out - segment.in) / segment.text.length;
        var sentences = tightSplit(segment.text, endSentenceDelimiter);
  
        if (sentences[sentences.length - 1].length === 0) {
          sentences.pop(); // remove any empty element at the end of each array
        }
  
        console.log("Pass 1.1");
        console.log(sentences);
  
        var subsStage1 = [];
  
        // first pass - check sentences are under idealSubLength
        // otherwise split further by commas
  
        sentences.forEach(function(v, i) {
  
          if (sentences[i].length > idealSubLength) {
  
            var fragments = tightSplit(sentences[i], midSentenceDelimiter);
  
            console.log("Pass 1.2");
            console.log(fragments);
  
            // the following code block ensures that we concatanate fragments
            // of size under minSubLength to the previous or next fragment
  
            fragments.forEach(function(w, j) {
              if (fragments[j+1] && fragments[j].length < minSubLength) {
                if (j > 0 && fragments[j-1].length < fragments[j+1].length) {
                  fragments[j-1] = fragments[j-1] + fragments[j];
                  fragments.splice(j, 1);
                } else {
                  fragments[j+1] = fragments[j] + fragments[j+1];
                  fragments.splice(j, 1);
                }
              }
            });
  
            console.log("Pass 1.3");
            console.log(fragments);
  
            var fragmentGroup = '';
  
            fragments.forEach(function(w, j) {
  
              // check to see if adding this fragment to the fragment group
              // takes it over the ideal subtitle length, if it does push
              // the fragment group and start with the new fragment
  
              if ((fragmentGroup + fragments[j]).length > idealSubLength) {
                if (fragmentGroup.length > 0) {
                  subsStage1.push(fragmentGroup);
                  fragmentGroup = fragments[j];
  
                  // if it's the last fragment push it regardless
                  if (j == fragments.length - 1) {
                    subsStage1.push(fragments[j]);
                  }
                } else { // this means fragment is over the idealSubLength - push it!
                  console.log(fragments[j]);
                  subsStage1.push(fragments[j]);
                }
              } else { // keep adding the fragment to fragmentGroup, if last fragment push
                fragmentGroup = fragmentGroup + fragments[j];
                if (j == fragments.length - 1) {
                  subsStage1.push(fragmentGroup);
                }
              }
            });
          } else {
            subsStage1.push(v);
          }
        });
  
        console.log("Pass 1.4");
        console.log(subsStage1);
  
        // second pass - check new 'fragments' are under idealSubLength
        // otherwise split into words
  
        var subsStage2 = [];
  
        subsStage1.forEach(function(v, i) {
          if (subsStage1[i].length > idealSubLength) {
            var fragments = splitIntoWords(subsStage1[i]);
  
            var fragmentGroup = '';
  
            fragments.forEach(function(w, j) {
  
              // if adding the word to the fragment group takes it over ideaSubLength
              // push and start with a new fragment.
  
              if ((fragmentGroup + fragments[j]).length > idealSubLength) {
                if (fragmentGroup.length > 0) {
                  subsStage2.push(fragmentGroup);
                  fragmentGroup = fragments[j];
  
                  if (j == fragments.length - 1) {
                    subsStage2.push(fragments[j]);
                  }
                } else {
                  subsStage2.push(fragments[j]);
                }
              } else {
                fragmentGroup = fragmentGroup + fragments[j];
                if (j == fragments.length - 1) {
                  subsStage2.push(fragmentGroup);
                }
              }
            });
          } else {
            subsStage2.push(v);
          }
        });
  
        console.log("Pass 2.1");
        console.log(subsStage2);
  
  
        // find short fragments and see if we can add them to previous or next fragment
  
        subsStage2.forEach(function(v, i) {
          var prevSub = '';
          var nextSub = v;
  
          if (i > 0) {
            prevSub = v + subsStage2[i-1];
          }
  
          if (i < subsStage2.length - 1) {
            nextSub = v + subsStage2[i+1];
          }
  
          if (v.length < minSubLength) { //too short, let's move it if we can
            if (i === 0 && nextSub.length <= maxSubLength) {
              subsStage2[1] = nextSub;
              subsStage2.splice(0, 1); //remove first element from array
            } else if (prevSub.length <= maxSubLength) {
  
              // if the next word has a delimiter add it to previous fragment if there is one
              if (i > 0) {
                if (v.match(midSentenceDelimiter) || v.match(endSentenceDelimiter)) {
                  subsStage2[i-1] = subsStage2[i-1] + v;
                  subsStage2.splice(i, 1);
                } else if (nextSub.length < prevSub.length && nextSub.length <= maxSubLength) {
                    subsStage2[i+1] = nextSub;
                    subsStage2.splice(i, 1);
                } else if (prevSub.length <= maxSubLength) {
                    subsStage2[i+1] = prevSub;
                    subsStage2.splice(i, 1);
                }
              }
            }
          }
        });
  
        console.log("Pass 2.2");
        console.log(subsStage2);
  
        //remove excess spaces at the start of each element
  
        subsStage2.forEach(function(v, i) {
          subsStage2[i] = v.trim();
        });
  
        console.log("Pass 2.3");
        console.log(subsStage2);
  
        // Add timings
  
        var starts = [];
        var stops = [];
  
        var lastOut = 0;
        var gapBetweenSubs = 0.001;
  
        subsStage2.forEach(function(v, i) {
          var out = v.length * letterDuration;
          starts[i] = formatSeconds(segment.in + lastOut);
          stops[i] = formatSeconds(segment.in + out + lastOut - gapBetweenSubs);
          lastOut = out + lastOut;
        });
  
        console.log("start times:" + starts);
        console.log("end times:" + stops);
  
        subsStage2.forEach(function(v, i) {
          var vttTiming;
          var srtTiming;
  
          // timing for TWO strings
  
          if (i < subsStage2.length - 1) {
            vttTiming = starts[i] + ' --> ' + stops[i+1];
            srtTiming = starts[i].replace('.', ',') + ' --> ' + stops[i+1].replace('.', ',');
          } else {
            vttTiming = starts[i] + ' --> ' + stops[i];
            srtTiming = starts[i].replace('.', ',') + ' --> ' + stops[i].replace('.', ',');;
          }
  
          // format over two lines
  
          if (i % 2 == 0) {
            vttOut += '\n\n' + vttTiming + '\n' + v;
            srtOut += '\n\n' + srtIndex + '\n' + srtTiming + '\n' + v;
            srtIndex++;
          } else {
            vttOut += '\n' + v;
            srtOut += '\n' + v;
          }
  
          ttmlOut += '<p begin="' + starts[i] + '" end="' + stops[i] + '">' + escapeText(v) + '</p>\n';
  
        });
      });
  
      ttmlOut += '</div>\n</body>\n</tt>';
  
      console.log("========= WebVTT ==========");
      console.log(vttOut);
      console.log("===========================");
      console.log("========== SRT ============");
      console.log(srtOut);
      console.log("===========================");
      console.log("========== TTML ===========");
      console.log(ttmlOut);
      console.log("===========================");

      document.getElementById('vtt').setAttribute("src", 'data:text/vtt,'+encodeURIComponent(vttOut));
  }

  function formatSeconds(seconds) {
    return new Date(seconds.toFixed(3) * 1000).toISOString().substr(11, 12);
  }

  function tightSplit(str, separator) {
    var arr = str.replace(separator, '$&※').split('※');
    return arr;
  }

  function splitIntoWords(str) {
    // add language specific splitters here
    var wordDelimiter = /[ ]/g;
    words = tightSplit(str, wordDelimiter);
    return words;
  }

  function escapeText(str) {
    return str;
  }

  cap.init = function(transcriptId) {
    var transcript = document.getElementById(transcriptId);
    var words = transcript.querySelectorAll('[data-m]');

    console.log("CAPTION WORDS");
    console.log(words);

    var data = {};
    data.segments = [];
    var segmentIndex = 0;

    function Detail(start, stop, text) {
      this.in = start;
      this.out = stop;
      this.text = text;
    }

    var detail = {
      "in": null,
      "out": null,
      "text": ""
    }

    var thisDetail;
    //var shouldSplit = false;

    for (var i = 0; i < words.length; ++i) {
      //console.log(words[i]);
      if (words[i].classList.contains("speaker")){
        console.log("speaker or should split");

        if (segmentIndex > 0) {
          var duration = 0;
          if (words[i-1].getAttribute("data-d") !== null) {
            duration = parseInt(words[i-1].getAttribute("data-d"))/1000;
          }
          thisDetail.out = parseInt(words[i-1].getAttribute("data-m"))/1000 + duration;
          data.segments.push(thisDetail);
        }

        thisDetail = new Detail(parseInt(words[i].getAttribute("data-m"))/1000,"","");
        
        console.log("segmentIndex = "+segmentIndex);
        console.log(data);
        segmentIndex++;

      } else {
        if (words[i].getAttribute("data-d") !== null) {
          if (i > 0 && thisDetail.text.length > 10 && parseInt(words[i].getAttribute("data-m")) - parseInt(words[i-1].getAttribute("data-m")) > 750) {
            var duration = 0;
            if (words[i-1].getAttribute("data-d") !== null) {
              duration = parseInt(words[i-1].getAttribute("data-d"))/1000;
            }
            thisDetail.out = parseInt(words[i-1].getAttribute("data-m"))/1000 + duration;
            data.segments.push(thisDetail);
            thisDetail = new Detail(parseInt(words[i].getAttribute("data-m"))/1000,"","");
            segmentIndex++;
          }

          thisDetail.text += words[i].innerText;
        }
      }
    }

    thisDetail.out = parseInt(words[words.length-1].getAttribute("data-m"))/1000 + parseInt(words[words.length-1].getAttribute("data-d"))/1000;
    data.segments.push(thisDetail);
    console.log("data");
    console.log(data);
    createCaptions(data);
  }

  return cap;

});