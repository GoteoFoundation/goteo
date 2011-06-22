
if (!('Superform' in window)) {

    $(function () {

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

                $(el).click(handler).find(':input').focus(function (event) {

                });;  

            },

            update: function (el, success) {
            
                el = $(el);

                el.is('li.element') || (el = el.parents('li.element').eq(0));
                                
                if (el.length) {
                
                    if (el.__updating) {
                        el.__updating.abort();
                    }                    

                    var frm = $(el).parents('form').eq(0);
                    
                    if (frm) {
                    
                        // Thank you, jQuery. All [0] on you.
                        var id = el.attr('id');
                        
                        el.__updating = $.ajax({
                            type:       'POST',
                            url:        frm.attr('target'),
                            cache:      false,
                            data:       frm.serialize(),
                            success:    function (data, status, xhr) {                             

                                            try {

                                                var s = data.indexOf('<!-- SFEL#' + id + ' -->', 0);

                                                if (s > 0) {
                                                    var e = data.indexOf('<!-- /SFEL#' + id + ' -->', s);
                                                    if (e > 0) {

                                                        var html = data.substring(s, e);

                                                        var nel = $('<div></div>');                                                        
                                                        nel[0].innerHTML = html;
                                                        
                                                        delete data;
                                                        

                                                        $(function () {       
                                                        
                                                            setTimeout(function () {    
                                                        
                                                                var nli = nel.find('li');

                                                                el.attr('class', nli.attr('class'));

                                                                // Get focused element
                                                                var focused = $(':focus');

                                                                var children = el.children('div.children').eq(0);
                                                                var contents = el.children('div.contents').eq(0);
                                                                var feedback = el.children('div.feedback').eq(0);

                                                                var nchildren = nli.children('div.children').eq(0);
                                                                var ncontents = nli.children('div.contents').eq(0);
                                                                var nfeedback = nli.children('div.feedback').eq(0);

                                                                // Copy new class
                                                                if (!focused.is(':input') || !$.contains(contents[0], focused[0])) {
                                                                    contents.replaceWith(ncontents);
                                                                    delete contents;
                                                                }
                                                                                                                                
                                                                if (nfeedback.length) {
                                                                    feedback.html(nfeedback.html());
                                                                    nfeedback.remove();
                                                                }
                                                                
                                                                if (children.length) {    
                                                                    children.find('li.element').each (function (i, child) {                                                                        
                                                                        // Thanks, jQuery                                                                                                                                                
                                                                        if (!($.contains(child, focused[0]))) {
                                                                            var newChild = nchildren.find('li.element#' + $(child).attr('id'));
                                                                            if (newChild.length) {
                                                                                $(child).replaceWith(newChild);
                                                                                $(child).remove;
                                                                                delete child;
                                                                            }
                                                                        }                                                                    
                                                                        
                                                                    });
                                                                }                                                                                                                                                                                                                                                                

                                                            }, 50); // setTimeout
                                                            
                                                        }); // $

                                                    }
                                                }

                                            } catch (e) {}



                                        }
                        }); // el.__updating = $.ajax();
                    }

                }
            }

        };
        
        var sf = $('#<?php echo $this['id'] ?>');
        
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