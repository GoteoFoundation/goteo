<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$key = $this['apikey'];

// contenido
$content = explode('<hr />', $this['page']->content);

if (empty($key)) {
    echo $content[0];
} else {
    echo str_replace('%APIKEY%', $key, $content[1]);
}

if (!empty($this['errors'])) echo implode('<br />', $this['errors']);