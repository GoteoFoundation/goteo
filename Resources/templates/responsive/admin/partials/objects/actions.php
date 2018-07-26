<?php

$value = $this->raw('value');
$ob = $this->raw('ob');

foreach($value as $k => $link) {
    $t = $this->text("regular-$k");
    if($k == 'edit') {
        $t = '<span class="fa fa-pencil" title="' . $t . '"></span>';
    }
    if($k == 'translate') {
        $t = '<span class="fa fa-globe" title="' . $t . '"></span>';
    }
    echo '<a class="btn btn-sm btn-default" href="' . $link . '">' . $t . '</a> ';
}

