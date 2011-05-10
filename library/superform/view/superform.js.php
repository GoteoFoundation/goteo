
if (!('Superform' in window)) {

    $(function () {

        window.Superform = {        
            
            init: function (frm) {

                frm = $(frm);
                
                if (frm.length > 0) {

                    frm = frm.eq(0);
                    
                    //frm.attr('target', 'superformiframe');

                    frm.__sf_al = 200; // Animations length

                    // Hints & errors                                
                    frm.__sf_fb = null; // Currently open feedback            

                    frm.find('li.element').each(function (i, el) {
                        Superform.bindElement(el);                
                    });

                    /*
                    frm.find(':input').focus(function (event) {
                        var p = $(this).parents('li.element');  
                        p.each(function (i, e) {
                            var id = $(e).attr('id');
                            if (frm.find('div.feedback#superform-feedback-for-' + id).length > 0) {
                                handler.apply(e, [event]);   
                                return false;
                            }               
                        });                
                        return false;                
                    });
                    */
                }
            },

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
                        console.log(frm.find('div.feedback#superform-feedback-for-' + id));
                        
                            frm.find('div.feedback#superform-feedback-for-' + id).fadeIn(frm.__sf_as);
                            frm.__sf_fb = id;
                        });

                    }

                    event.stopPropagation();

                };

                $(el).click(handler).find(':input').focus(function (event) {

                });;  

            },

            update: function (el) {

                el = $(el);        

                if (!el.is('li.element')) {
                    el = el.parents('li.element').eq(0);
                }

                if (el) {

                    var frm = $(el).parents('form').eq(0);
                    
                    if (frm) {

                        var id = el.addClass('busy').attr('id');

                        $.ajax({
                            type:       'POST',
                            url:        frm.attr('target'),
                            data:       frm.serialize(),
                            success:    function (data, status, xhr) {                       
                            
                                            try {
                                                
                                                var iframe = $('<iframe name="superformiframe" style="display: none"></iframe>');
                                                                                                                                                
                                                iframe.appendTo(document.body);
                                                
                                                var doc = iframe[0].contentWindow.document;
                                                
                                                doc.open();
                                                doc.write(data);
                                                doc.close();
                                                
                                                setTimeout(function () {
                                                
                                                    var li = iframe.contents().find('li.element#' + id);
                                                    
                                                    if (li.length > 0) {
                                                        li.addClass('busy');
                                                        el = el.replaceWith(li);                                                        
                                                    }
                                                    
                                                    //iframe.remove();                                                    
                                                    
                                                    li.removeClass('busy');
                                                    
                                                }, 1000);                                                                                                                                                                                                
                                                                                                                                                
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
        
        Superform.init('#<?php echo $this['id'] ?>');
        
    }); 
    
}