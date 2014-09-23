$(function () {

    var li = $('#<?php echo $this['id'] ?>');

    var select = li.children('div.contents').find('select');

    select.unbind('change');

    li.delegate('div.contents select', 'change', function () {
        li.superform();
    });
});


