<?php

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<div class="widget project-summary">
    
    <h<?php echo $level ?>>Envia un mensaje al autor</h<?php echo $level ?>>
        
    <form method="post" action="/message/<?php echo $project->id; ?>">
        <textarea name="message" cols="50" rows="5"></textarea>
        <br />
        <input type="submit" value="Enviar" />
    </form>

</div>