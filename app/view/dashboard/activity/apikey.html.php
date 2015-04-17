<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$key = $vars['apikey'];

// contenido
$content = explode('<hr />', $vars['page']->content);

if (empty($key)) {
    echo $content[0];
} else {
    echo str_replace('%APIKEY%', $key, $content[1]);
}

if (!empty($vars['errors'])) echo implode('<br />', $vars['errors']);
