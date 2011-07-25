<?php
use Goteo\Library\Text;

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<script type="text/javascript">
    function answer(id) {
        $('#thread').val(id);
        $('#message-text').val('<?php echo Text::get('project-messages-send_message-your_answer'); ?>').focus().select();
    }
</script>

<div class="widget project-message">
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-messages-send_message-header'); ?></h<?php echo $level ?>>

    <div>
        <form method="post" action="/message/<?php echo $project->id; ?>">
            <input type="hidden" id="thread" name="thread" value="" />
            <textarea id="message-text" name="message" cols="50" rows="5"></textarea>
            <button class="green" type="submit"><?php echo Text::get('project-messages-send_message-button'); ?></button>
        </form>
    </div>
</div>

<div class="widget project-messages">
    
    
    <div id="project-messages">
        
		<?php foreach ($project->messages as $message) : ?>
                <div class="message<?php if ($message->user->id == $project->owner) echo ' owner'; ?>">
                   <span class="avatar"><img src="/image/<?php echo $message->user->avatar->id; ?>/50/50" alt="" /></span>
                   <h<?php echo $level ?> class="user"><?php echo htmlspecialchars($message->user->name); if ($message->blocked == 1) echo ' ' . Text::get('regular-looks_for'); ?></h<?php echo $level ?>>
                   <div class="date"><span><?php echo $message->date ?></span></div>                   
                   <blockquote><?php echo $message->message; ?></blockquote>                   
                   <div class="actions">
                        <a class="" href="#" onclick="answer('<?php echo $message->id; ?>')"><?php echo Text::get('project-messages-answer_it'); ?></a>
                        <?php // si puede borrar este mensaje
                        if (\Goteo\Core\ACL::check("/message/delete/{$message->id}/{$project->id}")) : ?>
                                <a href="/message/delete/<?php echo $message->id; ?>/<?php echo $project->id; ?>"><?php echo Text::get('regular-delete'); ?></a>
                        <?php endif ?>
                   </div>
                                
               </div>

               <?php if (!empty($message->responses)) : 
                    foreach ($message->responses as $child) : ?>
                       <div class="child<?php if ($child->user->id == $project->owner) echo ' owner'; ?>">
                           <span class="avatar"><img src="/image/<?php echo $child->user->avatar->id; ?>/40/40" /></span>
                           <h<?php echo $level ?> class="user"><?php echo $child->user->name; ?></h<?php echo $level ?>>
                           <div class="date"><span><?php echo $child->date; ?></span></div>
                           <blockquote><?php echo $child->message; ?></blockquote>
                           <?php // si puede borrar este mensaje
                           if (\Goteo\Core\ACL::check("/message/delete/{$child->id}/{$project->id}")) : ?>
                           <div class="actions">
                                <a href="/message/delete/<?php echo $child->id; ?>/<?php echo $project->id; ?>"><?php echo Text::get('regular-delete'); ?></a>
                           </div>
                           <?php endif; ?>
                       </div>
                <?php endforeach;
                endif; ?>
		<?php endforeach; ?>
    </div>
    
    
</div>