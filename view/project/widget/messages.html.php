<?php

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<script type="text/javascript">
    function answer(id) {
        $('#thread').val(id);
        $('#message-text').val('Escribe tu respuesta aquí').focus().select();
    }
</script>

<div class="widget project-message">
    <h<?php echo $level ?> class="title">Escribe tu mensaje</h<?php echo $level ?>>

    <div>
        <form method="post" action="/message/<?php echo $project->id; ?>">
            <input type="hidden" id="thread" name="thread" value="" />
            <textarea id="message-text" name="message" cols="50" rows="5"></textarea>
            <input class="button" type="submit" value="Enviar" />
        </form>
    </div>
</div>

<div class="widget project-messages">
    
    
    <div id="project-messages">
		<?php foreach ($project->messages as $message) : ?>
                <div class="thread">
                   <img src="/image/<?php echo $message->user->avatar->id; ?>/50/50" />
                   <span class="user"><?php echo $message->user->name; ?></span>
                   <span class="when"><?php echo $message->date; ?></span>
                   <a href="#" onclick="answer('<?php echo $message->id; ?>')">[Responder]</a>
                   <?php // si puede borrar este mensaje
                   if (\Goteo\Core\ACL::check("/message/delete/{$message->id}/{$project->id}")) : ?>
                        <a href="/message/delete/<?php echo $message->id; ?>/<?php echo $project->id; ?>">[Borrar]</a>
                   <?php endif; ?>
                   <blockquote><?php echo $message->message; ?></blockquote>
               </div>

               <?php if (!empty($message->responses)) : 
                    foreach ($message->responses as $child) : ?>
                       <div class="child" style="margin-left:50px;">
                           <img src="/image/<?php echo $child->user->avatar->id; ?>/40/40" />
                           <span class="user"><?php echo $child->user->name; ?></span>
                           <span class="when"><?php echo $child->date; ?></span>
                           <?php // si puede borrar este mensaje
                           if (\Goteo\Core\ACL::check("/message/delete/{$child->id}/{$project->id}")) : ?>
                                <a href="/message/delete/<?php echo $child->id; ?>/<?php echo $project->id; ?>">[Borrar]</a>
                           <?php endif; ?>
                           <br />
                           <blockquote><?php echo $child->message; ?></blockquote>
                       </div>
                <?php endforeach;
                endif; ?>
		<?php endforeach; ?>
    </div>
    
    
</div>