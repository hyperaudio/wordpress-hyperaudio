var $ = jQuery; // needed for wordpress

$(document).ready(function() {
  var p = document.getElementById('para-split');
  var cp = document.getElementById('current-para-split');
  var paraSplitTime = p.value;
  var paraPunct = $('#para-punctuation').prop('checked');

  p.addEventListener(
    'input',
    function() {
      cp.innerHTML = p.value;
      paraSplitTime = p.value;
    },
    false
  );

  $('#para-punctuation').change(function() {
    if (this.checked) {
      paraPunct = $('#para-punctuation').prop('checked');
    }
  });

  $('#markup-view').click(function() {
    $('#rendered-view').addClass('inactive');
    $(this).removeClass('inactive');
    $('#rtranscript').hide();

    var regex = /\span>(.*?)\<span/g;

    var strToMatch = $('#rtranscript').html();

    while ((matches = regex.exec(strToMatch)) != null) {
      if (matches[1].length > 0) {
        strToMatch = strToMatch.replace("</span>"+matches[1], matches[1]+"</span>");
      } 
    }

    $('#htranscript').val(strToMatch);
    $('#htranscript').show();
    return false;
  });

  $('#rendered-view').click(function() {
    $('#markup-view').addClass('inactive');
    $(this).removeClass('inactive');
    $('#htranscript').hide();
    $('#rtranscript').html("<span>rendering...</span>");
    $('#rtranscript').show();
    
    setTimeout(renderTranscript, 100);

    return false;
  });

  function renderTranscript() {
    $('#rtranscript').html($('#htranscript').val());

    //document.getElementById("gen-subs").addEventListener("click", genSubs);
    $('#gen-subs').click(genSubs);
    //document.getElementById("generate-captions").style.display = 'inline';
    $('#generate-captions').show();
  
    function genSubs(){
      var cap1 = caption();
      var subs = cap1.init("rtranscript", null, null, null);
      //console.log(subs.vtt);
      //console.log(subs.srt);
      //var downloadLinkVtt = document.getElementById("download-vtt");
      //downloadLinkVtt.setAttribute("href", 'data:text/vtt,'+encodeURIComponent(subs.vtt));
      //downloadLinkVtt.style.display = 'inline';
      $('#download-vtt').attr("href", 'data:text/vtt,'+encodeURIComponent(subs.vtt)).show();

      //var downloadLinkSrt = document.getElementById("download-srt");
      //downloadLinkSrt.setAttribute("href", 'data:text/vtt,'+encodeURIComponent(subs.srt));
      //downloadLinkSrt.style.display = 'inline';
      $('#download-srt').attr("href", 'data:text/vtt,'+encodeURIComponent(subs.srt)).show();
    };
  }

  String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
  };

  // From popcorn.parserSRT.js

  function parseSRT(data) {

    var i = 0,
      len = 0,
      idx = 0,
      lines,
      time,
      text,
      sub;

    // Simple function to convert HH:MM:SS,MMM or HH:MM:SS.MMM to SS.MMM
    // Assume valid, returns 0 on error

    var toSeconds = function(t_in) {
      var t = t_in.split(':');

      try {
        var s = t[2].split(',');

        // Just in case a . is decimal seperator
        if (s.length === 1) {
          s = t[2].split('.');
        }

        return (
          parseFloat(t[0], 10) * 3600 +
          parseFloat(t[1], 10) * 60 +
          parseFloat(s[0], 10) +
          parseFloat(s[1], 10) / 1000
        );
      } catch (e) {
        return 0;
      }
    };

    var outputString = '<article><section><p>';
    var lineBreaks = $('#line-breaks').prop('checked');
    var ltime = 0;
    var ltext;

    // Here is where the magic happens
    // Split on line breaks
    lines = data.split(/(?:\r\n|\r|\n)/gm);
    len = lines.length;

    for (i = 0; i < len; i++) {
      sub = {};
      text = [];

      sub.id = parseInt(lines[i++], 10);

      // Split on '-->' delimiter, trimming spaces as well

      try {
        time = lines[i++].split(/[\t ]*-->[\t ]*/);
      } catch (e) {
        alert('Warning. Possible issue on line ' + i + ": '" + lines[i] + "'.");
        break;
      }

      sub.start = toSeconds(time[0]);

      // So as to trim positioning information from end
      if (!time[1]) {
        alert('Warning. Issue on line ' + i + ": '" + lines[i] + "'.");
        return;
      }

      idx = time[1].indexOf(' ');
      if (idx !== -1) {
        time[1] = time[1].substr(0, idx);
      }
      sub.end = toSeconds(time[1]);

      // Build single line of text from multi-line subtitle in file
      while (i < len && lines[i]) {
        text.push(lines[i++]);
      }

      // Join into 1 line, SSA-style linebreaks
      // Strip out other SSA-style tags
      sub.text = text.join('\\N').replace(/\{(\\[\w]+\(?([\w\d]+,?)+\)?)+\}/gi, '');

      // Escape HTML entities
      sub.text = sub.text.replace(/</g, '&lt;').replace(/>/g, '&gt;');

      // Unescape great than and less than when it makes a valid html tag of a supported style (font, b, u, s, i)
      // Modified version of regex from Phil Haack's blog: http://haacked.com/archive/2004/10/25/usingregularexpressionstomatchhtml.aspx
      // Later modified by kev: http://kevin.deldycke.com/2007/03/ultimate-regular-expression-for-html-tag-parsing-with-php/
      sub.text = sub.text.replace(
        /&lt;(\/?(font|b|u|i|s))((\s+(\w|\w[\w\-]*\w)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)(\/?)&gt;/gi,
        '<$1$3$7>'
      );
      //sub.text = sub.text.replace( /\\N/gi, "<br />" );
      sub.text = sub.text.replace(/\\N/gi, ' ');

      var splitMode = 0;

      var wordLengthSplit = $('#word-length').prop('checked');

      // enhancements to take account of word length

      var swords = sub.text.split(' ');
      var sduration = sub.end - sub.start;
      var stimeStep = sduration / swords.length;

      // determine length of words

      var swordLengths = [];
      var swordTimes = [];

      var totalLetters = 0;
      for (var si = 0, sl = swords.length; si < sl; ++si) {
        totalLetters = totalLetters + swords[si].length;
        swordLengths[si] = swords[si].length;
      }

      var letterTime = sduration / totalLetters;
      var wordStart = 0;

      for (var si = 0, sl = swords.length; si < sl; ++si) {
        var wordTime = swordLengths[si] * letterTime;
        var stime;
        if (wordLengthSplit) {
          stime = Math.round((sub.start + si * stimeStep) * 1000);
        } else {
          stime = Math.round((wordStart + sub.start) * 1000);
        }

        wordStart = wordStart + wordTime;
        var stext = swords[si];

        if (stime - ltime > paraSplitTime * 1000 && paraSplitTime > 0) {

          var punctPresent =
            ltext && (ltext.indexOf('.') > 0 || ltext.indexOf('?') > 0 || ltext.indexOf('!') > 0);
          if (!paraPunct || (paraPunct && punctPresent)) {
            outputString += '</p><p>';
          }
        }

        outputString += '<span data-m="' + stime + '">' + stext + ' </span>';

        ltime = stime;
        ltext = stext;

        if (lineBreaks) outputString = outputString + '\n';
      }
    }
    return outputString + '</p></section></article>';
  }

  $('#transform').click(function() {
    $('#transform-spinner').show();
    $('#htranscript').val("converting...");
    setTimeout(generateTranscript, 100);
  });

  function generateTranscript() {

    var input = $('#subtitles').val();

    var ht;

    var format = $('#format-select').val();

    switch (format) {

      case 'oe':
        var data = JSON.parse(input);
        var items = ['<article><section>\n<p>'];
        $.each(data.content.paragraphs, function(key, val) {
          var paraStart = Math.round(val.start*1000);
          items.push(
            '\n<span class="speaker" data-m="' + paraStart + '" ' +
            'data-d="0">' +
              val.speaker +
            ' </span>'
          );

          var lastStart = 0;

          $.each(val.words, function(k, v) {
            if (typeof v.start !== 'undefined') {
              items.push(
                '\n<span data-m="' + Math.round(v.start) + '" ' +
                'data-d="' + Math.round(v.end - v.start) + '">' +
                  v.text +
                ' </span>'
              );
              lastStart = v.start;
            } else {

              if (k === 0) {
                lastStart = paraStart;
              } 

              items.push(
                '\n<span data-m="' + lastStart + '" ' +
                'data-d="0">' +
                  v.text +
                ' </span>'
              );
            }
          });
          items.push('</p><p>');
        });

        items.push('</p></section></article>');

        ht = items.join('');

        // remove empty paras

        ht = ht.split("<p></p>").join("");
        
        break;
        
      case 'google':
        var data = JSON.parse(input);
        
        var items = ['<article><section><p>'];
        
        $.each(data.response.results, function(key, val) {
          $.each(val.alternatives, function(k, v) {
            for (var i = 0; i < v.words.length; i++) {
              items.push(
                '<span data-d="' +
                  Math.round(parseFloat(v.words[i].endTime) * 1000 - parseFloat(v.words[i].startTime) * 1000) +
                  '" data-m="' +
                  Math.round(parseFloat(v.words[i].startTime) * 1000) +
                  '">' +
                  v.words[i].word +
                  ' </span>'
              );


              if (i > 0 && Math.round(parseFloat(v.words[i].startTime)) - Math.round(parseFloat(v.words[i-1].startTime)) > paraSplitTime && paraSplitTime > 0) {
                items.push('</p><p>');
              }
            }
          });
        });

        items.push('</p></section></article>');

        ht = items.join('');
        break;
        
      case 'speechmatics':
        var data = JSON.parse(input);
        var items = ['<article><section><p>'];
        $.each(data, function(key, val) {
          if (key == 'words') {
            for (var i = 0; i < val.length; i++) {
              var punct = "";
              if ((i+1) < val.length && val[i+1].name === ".") {
                punct = ".";
              } 

              if (val[i].name !== ".") {
                items.push(
                  '<span data-d="' +
                    Math.round(val[i].duration * 1000) +
                    '" data-c="' +
                    val[i].confidence +
                    '" data-m="' +
                    Math.round(val[i].time * 1000) +
                    '">' +
                    val[i].name + punct +
                    ' </span>'
                );
              }
              
              if (i > 0 && Math.round(parseFloat(val[i].time)) - Math.round(parseFloat(val[i-1].time)) > paraSplitTime && paraSplitTime > 0) {
                if ((paraPunct && punct === ".") || (paraPunct === false)) {
                  items.push('</p><p>');
                }
              }
            }
          }
        });

        items.push('</p></section></article>');

        ht = items.join('');
        break;

      case 'dpe':
        var data = JSON.parse(input);

        var words = data.words;
        var paras = data.paragraphs;
        var items = ['<article><section>'];

        $.each(words, function(i, word) {

          $.each(paras, function(j, para) {
            if (word.start === para.start) {
              items.push("<p>");

              if (para.speaker.length > 0){
                items.push('<span class="speaker" data-m="'+Math.round(para.start * 1000)+'" data-d="0">['+para.speaker+'] </span>');
              }
            }
          });

          items.push(
            '<span data-m="' +
              Math.round(word.start * 1000) +
              '" data-d="' +
              Math.round((word.end - word.start) * 1000) +
              '">' +
              word.text + " " +
              ' </span>'
          );

          $.each(paras, function(j, para) {
            if (word.end === para.end) {
              items.push("<p>");
            }
          });
        });

        items.push('</section></article>');

        ht = items.join('');
        break;

      case 'gentle':
        var data = JSON.parse(input);

        wds = data['words'] || [];
        transcript = data['transcript'];

        var trans = document.createElement('p');

        trans.innerHTML = '';

        var currentOffset = 0;
        var wordCounter = 0;
        var lastOutTime = 0;

        wds.forEach(function(wd) {
          var newlineDetected = false;

          if (wd.startOffset > currentOffset) {
            var txt = transcript.slice(currentOffset, wd.startOffset);
            newlineDetected = /\r|\n/.exec(txt);

            if (trans.lastChild) {
              trans.lastChild.text += txt + " ";
            } else {
              // this happens only at the beginning when offset not zero
              var span = document.createElement('span');
              var initialWd = document.createTextNode(txt + " ");
              var initialDatam = document.createAttribute('data-m');
              var initialDatad = document.createAttribute('data-d');

              span.appendChild(initialWd);
              initialDatam.value = 0;
              initialDatad.value = 0;
              span.setAttributeNode(initialDatam);
              span.setAttributeNode(initialDatad);
              trans.appendChild(span);
              trans.appendChild(span);
            }

            if (newlineDetected) {
              var lineBreak = document.createElement('br');
              trans.appendChild(lineBreak);
            }
            currentOffset = wd.startOffset;
          }

          var datam = document.createAttribute('data-m');
          var datad = document.createAttribute('data-d');

          var word = document.createElement('span');

          var startOffset = wd.startOffset;
          var endOffset = wd.endOffset + 1;

          var txt = transcript.slice(startOffset, endOffset);

          // Check to see if previous letter is a character
          // AND the one before it a space,
          // if so include it.

          var previousChar = transcript[startOffset-1];

          if (wordCounter > 0 && previousChar !== ' ' && transcript[startOffset-2] === ' ') {
            txt = previousChar + txt;
          }

          // Look ahead to see if next word's previous letter is a space ...
          // if it's a character we don't want to add a space to THIS word,
          // UNLESS the character before that is a space
          // because (for example) it might be a start of a hyphenated word.

          if (wordCounter + 1 < wds.length) {
            var nextWordCharBeforeStartIndex = wds[wordCounter + 1].startOffset - 1;
            if (transcript[nextWordCharBeforeStartIndex] === ' ' || transcript[nextWordCharBeforeStartIndex-1] === ' ') {
              if (!txt.endsWith(" ")){
                txt = txt + " ";
              }
            }
          }

          if(txt.startsWith(' ')){ //trim leading space
            txt = txt.substring(1);
          }
	
	        var wordText = document.createTextNode(txt);
          word.appendChild(wordText);

          if (wd.start !== undefined) {
            datam.value = Math.floor(wd.start * 1000);
            datad.value = Math.floor((wd.end - wd.start) * 1000);
          } else {
            // look ahead to the next timed word
            for (var i = wordCounter; i < wds.length - 1; i++) {
              if (wds[i + 1].start !== undefined) {
                datam.value = Math.floor(wds[i + 1].start * 1000);
                break;
              }
            }
            datad.value = '100'; // default duration when not known
          }

          if (datam.value < lastOutTime) {
            datam.value = lastOutTime + 1;
          }

          word.setAttributeNode(datam);
          word.setAttributeNode(datad);

          lastOutTime = parseInt(datam.value) + parseInt(datad.value);

          trans.appendChild(word);
          
          currentOffset = wd.endOffset;
          wordCounter++;
        });

        currentOffset = transcript.length;

        article = document.createElement('article');
        section = document.createElement('section');
      
        section.appendChild(trans);
        article.appendChild(section);

        ht = article.outerHTML;

        //newlines can cause issues within HTML tags
        ht = ht.replace(/(?:\r\n|\r|\n)/g, '');

        ht = ht.replace(new RegExp('</span><br>', 'g'), '</span></p><p>');

        // replace all unneeded empty paras
        ht = ht.replace(new RegExp('<p></p>', 'g'), '');

        break;

      case 'srt':
        ht = parseSRT(input);
        break;

      case 'other':
        var xmlString = input,
          parser = new DOMParser(),
          doc = parser.parseFromString(xmlString, 'text/xml');

        var transcript = doc.getElementsByTagName('section')[0];

        for (var i = 0; i < doc.getElementsByClassName('speaker').length; i++) {
          transcript.getElementsByClassName('speaker')[i].innerHTML =
            '[' +
            transcript.getElementsByClassName('speaker')[i].innerHTML.replace(': ', '') +
            '] ';
          var datam = document.createAttribute('data-m');
          var datad = document.createAttribute('data-d');
          datam.value = transcript
            .getElementsByClassName('speaker')
            [i].nextElementSibling.getAttribute('data-m');
          datad.value = '1';
          transcript.getElementsByClassName('speaker')[i].setAttributeNode(datam);
          transcript.getElementsByClassName('speaker')[i].setAttributeNode(datad);
        }

        var transcriptText = transcript.outerHTML;

        ht = '<article>' + transcriptText + '</article>';
    }

    $('#htranscript').val(ht);
    $('#rtranscript').html(ht);

    $('#transform-spinner').hide();
    return false;
  }
});