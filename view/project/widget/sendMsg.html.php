<?php
use Goteo\Library\Text;

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<div class="widget project-message">
    
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-messages-send_direct-header'); ?></h><?php echo $level ?>>
        
    <form method="post" action="/message/direct/<?php echo $project->id; ?>">
        <textarea name="message" cols="50" rows="5"></textarea>
        <button class="green" type="submit"><?php echo Text::get('project-messages-send_message-button'); ?></button>
    </form>

</div>