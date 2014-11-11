$(function () {

    var li = $('#<?php echo $this['id'] ?>').closest('li.element');

    var radios = li.children('div.contents').find('input[type="radio"]');

    radios.unbind('change');

    li.delegate('div.contents input[type="radio"]', 'change', function () {
        li.superform();
       });
    }
});
