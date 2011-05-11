
if (!('Superform' in window)) {

    $(function () {

        window.Superform = {        
            
            bindElement: function (el, al) {

                var handler = function (event) {
                
                    var id = $(this).attr('id');

                    var frm = $(this).parents('form').eq(0);                                        
                    
                    if (frm) {
                                        
                        if (frm.__sf_fb && frm.__sf_fb !== id) {
                            setTimeout(function () {                      
                                frm.find('div.feedback#superform-feedback-for-' + frm.__sf_fb).fadeOut(frm.__sf_as);
                            });
                        }

                        setTimeout(function () {               
                            frm.find('div.feedback#superform-feedback-for-' + id).fadeIn(frm.__sf_as);
                            frm.__sf_fb = id;
                        });

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
                        el[0].className += ' busy';
                        
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

                                                        var nli = nel.find('li');

                                                        $(function () {       

                                                            nli.addClass('busy');                                                            
                                                            el.attr('class', nli.attr('class'));

                                                            // Get focused element
                                                            var focused = $(':focus');

                                                            var children = el.children('div.children').eq(0);
                                                            var contents = el.children('div.contents').eq(0);
                                                            var feedback = el.children('div.feedback').eq(0);

                                                            var nchildren = nli.children('div.children').eq(0);
                                                            var ncontents = nli.children('div.contents').eq(0);
                                                            var nfeedback = nli.children('div.feedback').eq(0);

                                                            //feedback.replaceWith(nfeedback);

                                                            // Copy new class
                                                            
                                                            setTimeout(function () {

                                                                if (!focused.is(':input') || !$.contains(contents[0], focused[0])) {
                                                                    contents.replaceWith(ncontents);
                                                                }             

                                                                if (nfeedback.length) {
                                                                    feedback.html(nfeedback.html());
                                                                }

                                                                //el.replaceWith(nli);

                                                                if (success) {
                                                                    success.call();
                                                                }

                                                                el.removeClass('busy');
                                                            });
                                                            

                                                        });

                                                    }
                                                }

                                            } catch (e) {
                                                // Error handling
                                                el.removeClass('busy');
                                            }



                                        },
                            error:      function (xhr) {
                                            el.removeClass('busy');
                                        }                    
                        });
                    }

                }
            }

        };
        
        var sf = $('#<?php echo $this['id'] ?>');
        
        var cfb = false;
        
        sf.delegate('li.element', 'focusin', function (event) {
        
            var li = this;
            var fb = $(li).find('div.feedback#superform-feedback-for-' + this.id).eq(0);

            if (fb.length > 0) {
                        
                if (cfb && fb[0] !== cfb[0]) {
                
                    setTimeout(function () {
                        cfb.fadeOut(200);
                    });
                                                                                                
                }            
                
                setTimeout(function () {
                    cfb = fb.fadeIn(200);
                });                                        
            }                
            
        });
        
        $('#<?php echo $this['id'] ?>').delegate('li.element', 'click', function (event) {
            if (!sf.__cf) {
                //$(this).find(':input').first().focus();
            }
        });
        
    }); 
    
}