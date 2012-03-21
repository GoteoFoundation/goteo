<?php

use Goteo\Library\Text;

$project = $this['project'];
$Data    = $this['reportData'];

if (!$project instanceof Model\Project) {
    throw new Redirection('/admin/projects');
}

echo \trace($Data);
?>
