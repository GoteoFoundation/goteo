<?php
use Goteo\Library\Text,
    Goteo\Model\Message;

$project = $vars['project'];
$level = (int) $vars['level'] ?: 3;

?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    jQuery(document).ready(function ($) {
        //change div#preview content when textarea lost focus
        $("#message-text").blur(function(){
            $("#preview").html($("#message-text").val().replace(/\n/g, "<br />"));
        });

        //add fancybox on #a-preview click
        $("#a-preview").fancybox({
            'titlePosition'     : 'inside',
            'transitionIn'      : 'none',
            'transitionOut'     : 'none'
        });

    <?php if (isset($_GET['msgto']) && $support = Message::isSupport($_GET['msgto'])) : ?>
        $('#thread').val('<?php echo $_GET['msgto'] ?>');
        $('#message-text').val('<?php echo addslashes(Text::get('project-messages-send_message_support-your_answer', $support)) ?>').focus().select();
    <?php endif; ?>

    });

    function answer(id, text) {
        $('#thread').val(id);
        $('#message-text').val(text).focus().select();
    }
// @license-end
</script>
<?php if (!empty($_SESSION['user']) && $project->status >= 3) : ?>
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

		<?php foreach ($project->messages as $message) :
            // si es de colaboracion
            if ($support = Message::isSupport($message->id)) {
                $response = addslashes(Text::get('project-messages-send_message_support-your_answer', $support));
            } else {
                $response = addslashes(Text::get('project-messages-send_message-your_answer'));
            }
            ?>
                <div class="message<?php if ($message->user->id == $project->owner) echo ' owner'; ?>">
                   <span class="avatar">
                   <a href="/user/profile/<?php echo htmlspecialchars($message->user->id)?>" target="_blank">
                    <img src="<?php echo $message->user->avatar->getLink(50,50, true); ?>" alt="" />
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
                        <a class="" href="#" onclick="answer('<?php echo $message->id; ?>', '<?php echo $response ?>')"><?php echo Text::get('project-messages-answer_it'); ?></a>
                        <?php endif; ?>
                        <?php // puede borrar este mensaje si es el impulsor o un admin central o su nodo
                        if ( $_SESSION['user']->id == $project->owner
                            || ( isset($_SESSION['admin_node']) && $_SESSION['admin_node'] == \GOTEO_NODE )
                            || ( isset($_SESSION['admin_node']) && $project->node == $_SESSION['admin_node'] )
                            || isset($_SESSION['user']->roles['superadmin'])
                        ) : ?>
                                <a href="/message/delete/<?php echo $message->id; ?>/<?php echo $project->id; ?>"><?php echo Text::get('regular-delete'); ?></a>
                        <?php endif ?>
                   </div>

               </div>

               <?php if (!empty($message->responses)) :
                    foreach ($message->responses as $child) : ?>
                       <div class="child<?php if ($child->user->id == $project->owner) echo ' owner'; ?>">
                           <span class="avatar">
                           <a href="/user/profile/<?php echo htmlspecialchars($child->user->id) ?>" target="_blank">
	                           <img src="<?php echo $child->user->avatar->getLink(40, 40, true); ?>" />
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
                            <?php // puede borrar este mensaje si es el impulsor o un admin central o su nodo
                            if ( $_SESSION['user']->id == $project->owner
                                || ( isset($_SESSION['admin_node']) && $_SESSION['admin_node'] == \GOTEO_NODE )
                                || ( isset($_SESSION['admin_node']) && $project->node == $_SESSION['admin_node'] )
                                || isset($_SESSION['user']->roles['superadmin'])
                            ) : ?>
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
