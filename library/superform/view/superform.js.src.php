
if (!('Superform' in window)) {

    $(function () {
    
        var sf = $('#<?php echo $this['id'] ?>');

        window.Superform = {        
            
            bindElement: function (el, al) {

                var handler = function (event) {
                
                    var id = $(this).attr('id');

                    var frm = $(this).parents('form').eq(0);                                        
                    
                    if (frm) {
                                        
                        if (frm.__sf_fb && frm.__sf_fb !== id) {
                            frm.find('div.feedback#superform-feedback-for-' + frm.__sf_fb).fadeOut(frm.__sf_as);                            
                        }
                        
                        frm.find('div.feedback#superform-feedback-for-' + frm.__sf_fb).fadeOut(frm.__sf_as);
                        frm.__sf_fb = id;
                        
                    }

                    event.stopPropagation();

                };                

            },
            
            updateElement: function (el, nel) {      
                               
                try {                                                                            

                    $(function () {  

                        setTimeout(function () {    
                            
                            // Get focused element
                            var focused = $(':focus').first();
                                                       
                            // Contents
                            var contents = el.children('div.contents');
                            var ncontents = nel.children('div.contents');
                            
                            if (contents.length) {                                
                                if (!ncontents.length) {                                
                                    //contents.slideUp('fast', 'swing');
                                    //contents.remove();
                                } else if (!$.contains(contents[0], focused[0])) {
                                    contents.replaceWith(ncontents);
                                }
                            } else if (ncontents.length) {
                                el.append(ncontents);
                            }

                            // Feedback
                            var feedback = el.children('div.feedback');          
                            var nfeedback = nel.children('div.feedback');
                            if (nfeedback.length) {                                                                        
                                if (feedback.length) {
                                    feedback.html(nfeedback.html());
                                } else {
                                    el.append(nfeedback);
                                }                                        
                            } else if (feedback.length) {
                                feedback.remove();
                            }
                            
                            var children = el.children('div.children').children('div.elements').children('ol').children('li.element');
                            var nchildren = nel.children('div.children').children('div.elements').children('ol').children('li.element');
                                                        
                            
                            children.each(function (i, child) {
                                var nchild = nchildren.filter('div.element#' + child.id);
                                if (nchild.length) {                               
                                    Superform.updateElement(child, nchild);
                                    nchildren = nchildren.not(nchild);
                                } else {
                                    $(child).slideUp('fast', 'swing');                                    
                                }
                            });
                            
                            if (nchildren.length) {
                            
                                if (!children.length) {
                                    el.children('div.children').remove();
                                    var c = $('<div class="children"><div class="elements"><ol></ol></div></div>');
                                    el.append(c);
                                    
                                }
                                
                                setTimeout(function () {
                                    nchildren.hide();
                                    el.children('div.children').children('div.elements').children('ol').append(nchildren);
                                    nchildren.slideDown('fast', 'swing');                                    
                                });
                                
                            }
                            el.attr('class', nel.attr('class'));                            

                        }); // setTimeout

                    }); // $



                } catch (e) {
                
                    
                }
            },

            update: function (el, params, success) {
            
                if (typeof el === 'string') {
                    el = $('#' + el);
                } else {                                                    
                    el = $(el);
                }

                el.is('li.element') || (el = el.parents('li.element').eq(0));
                
                el.addClass('busy');
                                                                
                if (el.length) {
                
                    if (el.__updating) {
                        el.__updating.abort();
                    }                    

                    var frm = $(el).parents('form').eq(0);
                    
                    if (frm) {
                    
                        var id = el.attr('id');
                        
                        var data = frm.serializeArray();
                        
                        if (params) {
                            $.each(params, function (k, v) {
                                data.push({
                                    name: k,
                                    value: v
                                });
                            });
                        }         
                        
                        
                        el.__updating = $.ajax({
                            type:       'POST',
                            url:        frm.attr('action'),
                            cache:      false,
                            data:       data,                            
                            success:    function (html, status, xhr) {                            
                            
                                            var s = html.indexOf('<!-- SFEL#' + id + ' -->', 0);
                                            
                                            if (s > 0) {

                                                var e = html.indexOf('<!-- /SFEL#' + id + ' -->', s);

                                                if (e > 0) {

                                                    var html = html.substring(s, e);

                                                    var wrp = $('<div></div>');                                                        
                                                    wrp[0].innerHTML = html;
                                                    delete html;                                                                                                        
                                                    
                                                    var nel = wrp.children().first();                                                    
                                                    
                                                    Superform.updateElement(el, nel);
                                                    
                                                }
                                                
                                            }                                            
                                        },
                            error: function () {
                                alert('Error -->' + frm.attr('action') + '<--');
                            }
                        }); // el.__updating = $.ajax();
                    }

                }
            }

        };                
        
        var cfb = false;
        
        sf.delegate('li.element', 'click focusin', function (event) {
        
            $(event.target).parents('li.element').each(function (i, li) {
            
                var fb = $(li).find('div.feedback#superform-feedback-for-' + li.id).not(':empty').first();
                                                                
                if (fb.length) {
                
                    setTimeout(function () {
                        sf.find('div.feedback').not(fb).fadeOut(200);
                    });                                                                                                
                                    
                    setTimeout(function () {
                        fb.fadeIn(200);
                    });
                    
                    return false;
                }
                
            });            
            
        });                
        
    }); 
    
}