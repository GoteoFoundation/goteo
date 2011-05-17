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
                   <br />
                   <blockquote><?php echo $message->message; ?></blockquote>
               </div>

               <?php if (!empty($message->responses)) : 
                    foreach ($message->responses as $child) : ?>
                       <div class="child" style="margin-left:50px;">
                           <img src="/image/<?php echo $child->user->avatar->id; ?>/40/40" />
                           <span class="user"><?php echo $child->user->name; ?></span>
                           <span class="when"><?php echo $child->date; ?></span><br />
                           <blockquote><?php echo $child->message; ?></blockquote>
                       </div>
                <?php endforeach;
                endif; ?>
		<?php endforeach; ?>
    </div>
    
    
</div>