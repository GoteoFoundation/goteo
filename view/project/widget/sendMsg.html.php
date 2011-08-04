<?php
use Goteo\Library\Text;

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
 <script type="text/javascript">
	// Mark DOM as javascript-enabled
	jQuery(document).ready(function ($) { 
	    //change div#preview content when textarea lost focus
		$("#message").blur(function(){
			$("#preview").html($("#message").val());
		});
		
		//add fancybox on #a-preview click
		$("#a-preview").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		});
	});
</script>
<div class="widget project-message">
    
    <h<?php echo $level ?> class="title"><?php echo Text::get('project-messages-send_direct-header'); ?></h<?php echo $level ?>>
        
    <form method="post" action="/message/direct/<?php echo $project->id; ?>">    	
    	<div id="bocadillo"></div>
        <textarea id="message" name="message" cols="50" rows="5"></textarea>
        
        <a target="_blank" id="a-preview" href="#preview" class="preview">&middot;Previsualizar</a>
        <div style="display:none">
        	<div id="preview" style="width:400px;height:300px;overflow:auto;">
                    
                </div>
        </div>
        
         
        
        <button <?php echo (!empty($_SESSION['user']->id)) ? 'class="green"' : 'class="disabled" disabled="disabled"'; ?> type="submit"><?php echo Text::get('project-messages-send_message-button'); ?></button>
    </form>

</div>