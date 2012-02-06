<?php

use Goteo\Core\Error,
    Goteo\Library\WallFriends,
    Goteo\Library\Text,
    Goteo\Model;

$project = $this['project'];

$wof = new WallFriends($project->id);
if (!$wof instanceof \Goteo\Library\WallFriends) return;

echo '<a name="wof" />';

//el enlace del widget


// directamente lo que devuelve la librería
echo $wof->html(580);
