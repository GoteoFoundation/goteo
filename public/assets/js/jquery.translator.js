/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Ivan Vergés

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/
/**
* jQuery Translator
* Translates a text using (currently) api from mymemory.translated.net
*
* Example:
* <code>
* $.Translator({pair: 'en|es', origin: 'English text'}, function(data){
*     if(data.result) {
*     }
*     else alert(data.text);
* });
* </code>
* @param options
* {
*     pair: Source and language pair, separated by the | symbol. Use ISO standard names or RFC3066. Example: en|es
*     origin: Text to translate
*     de: optional, provide an email to increase from 100 to 1000 requests per day
* }
*
* @param callback function called on result
* function(data) {
*     data.result: true or false if succeded
*     data.origin: original text
*     data.pair: original lang pair
*     data.text: text translated or error message
* }
* @author Ivan Vergés 2014
 */
(function($){
    /**
     * Splits a text into phrases with maxchar max length
     * @param  {[type]} txt [description]
     * @return {[type]}     [description]
     */
    var _get_pieces = function (txt, maxchars) {
        if( !(maxchars) ) maxchars = 500;
        var pieces = [];
        do {
            sub = txt.substr(0, maxchars);
            var pos = sub.lastIndexOf('.') + 1;
            if(pos < maxchars*0.7) {
                pos = sub.lastIndexOf('\n') + 1;
                if(pos < maxchars*0.7) {
                    pos = sub.lastIndexOf(';') + 1;
                    if(pos < maxchars*0.7) {
                        pos = sub.lastIndexOf(',') + 1;
                        if(pos < maxchars*0.7) {
                            pos = sub.lastIndexOf(' ');
                            if(pos < maxchars*0.7) {
                                pos = maxchars;
                            }
                        }
                    }
                }
            }
            pieces[pieces.length] = txt.substr(0, pos);
            txt = txt.substr(pos);

        } while(txt.length > 0);

        return pieces;
    };

    var TranslatorTask = function (options, callback) {
       //mymemory service translator
        var t = this;
        t.api = "https://api.mymemory.translated.net/get";
        t.provider = "mymemory"; //values: mymemory, yandex (apikey must be provided)
        t.maxchars = 500;
        t.response = {result:false, text:''};
        t.ops = {};
        t.callback = $.isFunction(callback) ? callback : function(){};

        if(typeof options !== 'object') {
            t.response.text = 'Undefined options object!\nUse: {pair:"en|es", origin:"English origin text to translate"}';
        }
        else if(options.pair === undefined || options.pair === null) {
            t.response.text = 'Undefined langpair!\nUse: es|en en|ca es|ca etc';
        }
        else if(options.origin === undefined || options.origin === null) {
            t.response.text = 'Undefined text to translate!';
        }
        else if(options.provider === 'yandex' && (options.apikey === undefined || options.apikey === null)) {
            t.response.text = 'An API KEY must be provided in order to use Yandex translation!';
        }
        else t.response.result = true;

        // console.log(options);
        if(t.response.result) {
            var txt = options.origin;
            txt = txt.replace(/<br/ig, '\n<br');
            //strip html
            txt = $("<div/>").html(txt).text();

            //default values for yandex
            if(options.provider === 'yandex') {
                t.provider = 'yandex';
                t.api = "https://translate.yandex.net/api/v1.5/tr.json/translate";
                t.ops = {
                    lang : options.pair.replace('|','-'),
                    key: options.apikey
                };
                t.maxchars = 10000;
            }
            else {
                t.ops = {langpair : options.pair};
                if(options.de) {
                    t.ops.de = options.de;
                }
                if(options.ip) {
                    t.ops.ip = options.ip;
                }
                //if we want to override the api url (ie: we want a https proxy)
                if(options.api) {
                    t.api = options.api;
                }
            }

            t.pieces = _get_pieces(txt, t.maxchars);

            // console.log(t.pieces);
            var results = [];
            var calls = [];
            var errors = false;
            $.each(t.pieces, function(index, value){
                var sops = t.ops;
                if(t.provider === 'yandex') {
                    sops.text = value;
                    calls.push(
                        $.post(t.api, sops).always(function(data) {
                            // console.log('done: [' + index + ' : '+value+']');
                            // console.log(data.responseJSON);
                            if(typeof data.responseJSON !== 'undefined' || data.code !== 200) {
                                //error on translate
                                results[index] = 'Error ' + data.responseJSON.code + ': ' + data.responseJSON.message + '\n';
                                errors = true;
                            }
                            else {
                                results[index] = data.text[0];
                            }
                        }, 'json')
                    );
                }
                else {
                    sops.q = value;
                    calls.push(
                        $.getJSON(t.api, sops).always(function(data) {
                            // console.log('done: [' + index + ' : '+value+']');
                            // console.log(data);
                            if(data.responseData.translatedText) {
                                results[index] = data.responseData.translatedText;
                            }
                            if(data.responseStatus !== 200) {
                                //error on translate
                                errors = true;
                            }
                        })
                    );
                }
            });
            $.when.apply(t, calls).always(function() {
                // console.log('ALL DONE: ');console.log(t);console.log('errors',errors);
                t.response.text = results.join("\n").trim();
                if(errors) t.response.result = false;
                t.callback(t.response);
            });
        }
        else {
            t.callback(t.response);
        }

        return t;

    };


    $.extend({
        Translator : function(options, callback) {
            return new TranslatorTask(options, callback);
        }
    });

})(jQuery);

