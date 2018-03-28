/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

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

$(function(){

    var setBar = function() {
        var $container = $('.dashboard-content>.inner-container');
        var $bar = $container.find('.costs-bar');

        var min = opt = 0;
        $container.find('.amount input').each(function() {
            var amount = parseInt($(this).closest('.panel-body').find('.amount input').val(), 10);
            var required = parseInt($(this).closest('.panel-body').find('.required select').val(), 10);
            if(amount) {
                if(required) {
                    min += amount;
                } else {
                    opt += amount;
                }
            }

        });
        $bar.find('.amount-min').html(min);
        $bar.find('.amount-opt').html(opt);
        $bar.find('.amount-total').html(min + opt);
        var per_min = Math.round(100*min/(min+opt));
        var per_opt = Math.round(100*opt/(min+opt));
        var min_w = parseInt($bar.find('.min').css('width', 'auto').width());
        var opt_w = parseInt($bar.find('.opt').css('width', 'auto').width());
        var total_w = parseInt($bar.find('.total').css('width', 'auto').width());
        console.log('calc', min+'€', opt+'€',per_min+'%',per_opt+'%',min_w+'px',opt_w+'px',total_w+'px');
        $bar.find('.min').css('width', per_min + '%').css({
            minWidth: min_w + 'px',
            maxWidth: 'calc(' + per_min + '% - ' + (total_w + opt_w) + 'px)'
        });
        $bar.find('.opt').css('width', (per_opt * 0.8) + '%').css({
            minWidth: opt_w + 'px',
            maxWidth: 'calc(' + per_opt + '% - ' + (total_w + min_w) + 'px)'
        });
        $bar.find('.bar-min').css('width', per_min + '%').html(per_min + '%');
        $bar.find('.bar-opt').css('width', per_opt + '%').html(per_opt + '%');
    };
    setBar();
    $('.autoform').on('change', '.cost-item .required select', function() {
        var required = parseInt($(this).val(), 10);
        var $panel = $(this).closest('.cost-item');
        if(required) {
            $panel.addClass('lilac');
        } else {
            $panel.removeClass('lilac');
        }
        setBar();
    });

    $('.autoform').on('change', '.cost-item .amount input', setBar);

    // Send the form via AJAX
    $('.autoform').on('click', '.add-cost', function(e){
        e.preventDefault();
        var $form = $(this).closest('form');
        var $list = $form.find('.cost-list');
        var serial = $form.serialize() + '&' + encodeURIComponent($(this).attr('name')) + '=';
        console.log('add cost', serial);

        $but = $(this).hide();
        $list.find('>.text-danger').remove();
        $list.append('<div class="loading"></div>');
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: serial
        }).done(function (data) {
            var $data = $(data);
            $list.append($data.hide());
            $data.slideDown();
        }).fail(function (data) {
            $list.append('<p class="text-danger">' + data.responseText + '</p>');
        }).always(function() {
            $but.show();
            $list.find('>.loading').remove();
        });
    });

    $('.autoform').on('click', '.remove-cost', function(e){
        if(e.isPropagationStopped()) return false;
        e.preventDefault();
        var $but = $(this);
        var $form = $but.closest('form');
        var $list = $form.find('.cost-list');
        var serial = $form.serialize() + '&' + encodeURIComponent($but.attr('name')) + '=';
        var $item = $but.closest('.panel');
        $but.hide().after('<div class="loading"></div>');
        $item.find(':input').attr('disabled', true);
        // console.log('del cost', serial);
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: serial
        }).done(function () {
            $item.slideUp(function(){
                $(this).remove();
                setBar();
            });
        }).fail(function (data) {
            console.log('An error occurred.', data);
            alert(data.responseText);
        }).always(function() {
            $but.show().next('.loading').remove();
        });


    });
});
