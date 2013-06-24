<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project;

$project = $this['project'];

if (!$project instanceof  Goteo\Model\Project) {
    return;
}

echo new View('view/project/graph.html.php', array('project'=>$project, 'other-data'=>$this['other-data']));
