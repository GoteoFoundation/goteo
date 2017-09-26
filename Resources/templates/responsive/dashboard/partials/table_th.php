<?php

$field = $this->field;
if(!$field) {
    return $this->text;
}
$order_var = 'order';

// URL to be added the page_var variable
$baselink =  (string) $this->baselink;
if(empty($baselink)) $baselink = $this->get_pathinfo() . '?' . $this->get_querystring();

if (strpos($baselink,'?') === false) $baselink .= '?';
else {
    list($baselink, $query) = explode('?', $baselink);
    parse_str($query, $parts);
    unset($parts[$order_var]);
    $query = http_build_query($parts);
    $join = '?';
    if($query) {
        $join = '&';
        $baselink = "$baselink?$query";
    }
}

// Which direction are we now?
// Default search from current query
$order = $this->order;
if(empty($order)) $order = $this->get_query($order_var);
list($key, $dir) = explode(' ', $order);
if(!in_array($dir, ['ASC', 'DESC'])) $dir = 'ASC';

$span = '';
if($key === $field) {
    if($dir === 'ASC') $dir = 'DESC';
    else $dir = 'ASC';
    $span = '&nbsp;<span class="fa fa-caret-' . ($dir === 'ASC' ? 'down' : 'up' ) . '"></span>';
}

?><a href="<?= $baselink . $join . "order=$field+$dir" ?>"><?= $this->text . $span ?></a>
