<?php
use Goteo\Library\Text;

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<script type="text/javascript">
	jQuery(document).ready(function ($) { 
	    //change div#preview content when textarea lost focus
		$("#message-text").blur(function(){
			$("#preview").html($("#message-text").val());
		});
		
		//add fancybox on #a-preview click
		$("#a-preview").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		});
	});

    function answer(id) {
        $('#thread').val(id);
        $('#message-text').val('<?php echo Text::get('project-messages-send_message-your_answer'); ?>').focus().select();
    }
</script>
<?php if (!empty($_SESSION['user'])) : ?>
<div class="widget project-message">
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-messages-send_message-header'); ?></h<?php echo $level ?>>

    <div>
        <form method="post" action="/message/<?php echo $project->id; ?>">
            <input type="hidden" id="thread" name="thread" value="" />
            <div id="bocadillo"></div>
            <textarea id="message-text" name="message" cols="50" rows="5"></textarea>
            <a target="_blank" id="a-preview" href="#preview" class="preview">&middot;<?php echo Text::get('regular-preview'); ?></a>
            <div style="display:none">
                <div id="preview" style="width:400px;height:300px;overflow:auto;">
                        
                    </div>
            </div>
            <button class="green" type="submit"><?php echo Text::get('project-messages-send_message-button'); ?></button>
        </form>
    </div>
</div>
<?php endif; ?>
<div class="widget project-messages">


    <div id="project-messages">

		<?php foreach ($project->messages as $message) : ?>
                <div class="message<?php if ($message->user->id == $project->owner) echo ' owner'; ?>">
                   <span class="avatar">
                   <a href="/user/profile/<?php echo htmlspecialchars($message->user->id)?>" target="_blank">
                    <img src="<?php echo SRC_URL ?>/image/<?php echo $message->user->avatar->id; ?>/50/50/1" alt="" />
                   </a>
                  </span>
                   <h<?php echo $level ?> class="user">
				   <a href="/user/profile/<?php echo htmlspecialchars($message->user->id)?>" target="_blank">
				   <?php echo htmlspecialchars($message->user->name); if ($message->blocked == 1) echo ' ' . Text::get('regular-looks_for'); ?>
                   </a>
                   </h<?php echo $level ?>>
                   <a name="message<?php echo $message->id; ?>"></a>
                   <div class="date"><span>Hace <?php echo $message->timeago ?></span></div>
                   <blockquote><?php echo $message->message; ?></blockquote>
                   <div class="actions">
                        <?php if (!empty($_SESSION['user'])) : ?>
                        <a class="" href="#" onclick="answer('<?php echo $message->id; ?>')"><?php echo Text::get('project-messages-answer_it'); ?></a>
                        <?php endif; ?>
                        <?php // si puede borrar este mensaje
                        if (\Goteo\Core\ACL::check("/message/delete/{$message->id}/{$project->id}")) : ?>
                                <a href="/message/delete/<?php echo $message->id; ?>/<?php echo $project->id; ?>"><?php echo Text::get('regular-delete'); ?></a>
                        <?php endif ?>
                   </div>

               </div>

               <?php if (!empty($message->responses)) :
                    foreach ($message->responses as $child) : ?>
                       <div class="child<?php if ($child->user->id == $project->owner) echo ' owner'; ?>">
                           <span class="avatar">
                           <a href="/user/profile/<?php echo htmlspecialchars($child->user->id) ?>" target="_blank">
	                           <img src="<?php echo SRC_URL ?>/image/<?php echo $child->user->avatar->id; ?>/40/40/1" />
                           </a>
                           </span>
                           <a name="message<?php echo $child->id; ?>" />
                           <h<?php echo $level ?> class="user">
						   <a href="/user/profile/<?php echo htmlspecialchars($child->user->id) ?>" target="_blank">
						   <?php echo $child->user->name; ?>
                           </a>
                           </h<?php echo $level ?>>
                           <div class="date"><span>Hace <?php echo $child->timeago; ?></span></div>
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
