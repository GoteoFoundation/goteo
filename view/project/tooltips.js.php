            <script type="text/javascript">
                                                                
            jQuery(document).ready(function($) {
                
                var frm = $('div.superform');
                
                frm.__currentTooltip = null;
                                                
                frm.find('li.field[id]').each(function (i, li) {
                    
                    li = $(li);
                    
                    var id = li.attr('id').substring(6,99999);
                    
                    var handler = function (event) {
                        
                        if (frm.__currentTooltip !== id) {                                                        
                            frm.find('div.tooltip').hide();
                            frm.find('div.tooltip#tooltip-' + id).fadeIn(300);
                            frm.__currentTooltip = id;                            
                        }
                        event.stopPropagation();
                    };
                    
                    li.bind('click', handler);
                    
                    li.find(':input').each(function (j, el) {
                        
                        var el = $(el);
                        var p = el.parents('li.field');
                        
                        if (p.length >= 1 && ($(p[0]).attr('id') === 'field-' + id)) {                            
                            el.bind('focus', handler);                                                        
                        }
                    });
                    
                });
                        
            });                
            </script>