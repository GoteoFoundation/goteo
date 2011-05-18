<?php

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<script type="text/javascript">
    function answer(id) {
        document.getElementById('thread').value = id;
        document.getElementById('message-text').value = 'Escribe tu respuesta aquí';
    }
</script>
<div class="widget project-summary">
    
    <h<?php echo $level ?>>Escribe tu mensaje</h<?php echo $level ?>>

    <div>
        <form method="post" action="/message/<?php echo $project->id; ?>">
            <input type="hidden" id="thread" name="thread" value="" />
            <textarea id="message-text" name="message" cols="50" rows="5"></textarea>
            <br />
            <input type="submit" value="Enviar" />
        </form>
    </div>

    <div id="project-messages">
		<?php foreach ($project->messages as $message) : ?>
                <div class="thread">
                   <img src="/image/<?php echo $message->user->avatar->id; ?>/50/50" />
                   <span class="user"><?php echo $message->user->name; ?></span>
                   <span class="when"><?php echo $message->date; ?></span>
                   <a href="#" onclick="answer('<?php echo $message->id; ?>')">[Responder]</a>
                   <?php // si puede borrar este mensaje
                   if (\Goteo\Core\ACL::check("/message/delete/{$message->id}")) : ?>
                        <a href="/message/delete/<?php echo $message->id; ?>/<?php echo $project->id; ?>">[Borrar]</a>
                   <?php endif; ?>
                   <br />
                   <?php // si puede editar este mensaje
                   if (\Goteo\Core\ACL::check("/message/edit/{$message->id}")) : ?>
                   <form method="post" action="/message/edit/<?php echo $message->id; ?>/<?php echo $project->id; ?>">
                        <textarea name="message" cols="50" rows="5"><?php echo $message->message; ?></textarea>
                        <br />
                        <input type="submit" value="Guardar" />
                   </form>
                   <?php else : ?>
                       <blockquote><?php echo $message->message; ?></blockquote>
                   <?php endif; ?>
               </div>

               <?php if (!empty($message->responses)) : 
                    foreach ($message->responses as $child) : ?>
                       <div class="child" style="margin-left:50px;">
                           <img src="/image/<?php echo $child->user->avatar->id; ?>/40/40" />
                           <span class="user"><?php echo $child->user->name; ?></span>
                           <span class="when"><?php echo $child->date; ?></span>
                           <?php // si puede borrar este mensaje
                           if (\Goteo\Core\ACL::check("/message/delete/{$child->id}")) : ?>
                                <a href="/message/delete/<?php echo $child->id; ?>/<?php echo $project->id; ?>">[Borrar]</a>
                           <?php endif; ?>
                           <br />
                       <?php // si puede editar este mensaje
                       if (\Goteo\Core\ACL::check("/message/edit/{$child->id}")) : ?>
                       <form method="post" action="/message/edit/<?php echo $child->id; ?>/<?php echo $project->id; ?>">
                            <textarea name="message" cols="50" rows="5"><?php echo $child->message; ?></textarea>
                            <br />
                            <input type="submit" value="Guardar" />
                       </form>
                       <?php else : ?>
                           <blockquote><?php echo $child->message; ?></blockquote>
                       <?php endif; ?>
                       </div>
                <?php endforeach;
                endif; ?>
		<?php endforeach; ?>
    </div>
    
    
</div>