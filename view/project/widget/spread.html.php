<?php
use Goteo\Library\Text;

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<div class="widget project-spread">
    
    <h<?php echo $level ?>><?php echo Text::get('project-spread-header'); ?></h<?php echo $level ?>>
        
    <div>
        <h<?php echo $level + 1?>><?php echo Text::get('project-spread-widget'); ?></h<?php echo $level + 1?>>
    </div>    
    
    <div>
        <h<?php echo $level + 1?>><?php echo Text::get('project-share-header'); ?></h<?php echo $level + 1?>>
    </div>

    
    
</div>