<?php

$value = $this->raw('value');
$ob = $this->raw('ob');

foreach($value as $k => $link) {
    $t = $this->text("regular-$k");
    $add = '';
    if($k == 'edit') {
        $t = '<span class="fa fa-pencil" title="' . $t . '"></span>';
    }
    if($k == 'translate') {
        $t = '<span class="fa fa-globe" title="' . $t . '"></span>';
    }
    if($k == 'preview') {
        $t = '<span class="fa fa-eye" title="' . $t . '"></span>';
        $add = ' target="_blank"';
    }
    if($k == 'delete') {
        $t = '<span class="fa fa-trash" title="' . $t . '"></span>';
    }
    if($k == 'clone') {
        $t = '<span class="fa fa-clone" title"' . $t . '"></span>';
    }
    if($k == 'details') {
        $t = '<span class="fa fa-info" title"' . $t . '"></span>';
    }
    if($k == 'download') {
        $t = '<span class="fa fa-download" title"' . $t . '"></span>';
    }

    echo '<a class="btn btn-sm btn-default" href="' . $link . '"' . $add . '>' . $t . '</a> ';
}
