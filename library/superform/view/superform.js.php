var Superform = {

    init: function (frm) {
    
        frm = $(frm);

        if (frm.length > 0) {

            frm = frm.eq(0);

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
                    dataType:   'html',
                    data:       frm.serialize(),
                    success:    function (data, status, xhr) {                       
                                    try {                   
                                        var nel = $('<div></div>').html(data).find('li.element#' + id);                                        
                                        el.removeClass('busy');
                                    } catch (e) {
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


$(function() {
    Superform.init('#<?php echo $this['id'] ?>');
}); 