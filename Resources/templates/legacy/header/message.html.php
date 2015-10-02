<?php
$messages = $_SESSION['messages'];
unset($_SESSION['messages']);
?>
<script type="text/javascript">
	  jQuery(document).ready(function ($) { 
		   $(".message-close").click(function (event) {
					$("#message").fadeOut(2000);
           });
	  });
</script>
    <div id="message">
    	<div id="message-content">
        	<input type="button" class="message-close" />
            <ul>
    <?php foreach($messages as $message): ?>
                <li>
                    <span class="ui-icon ui-icon-<?php echo $message->type ?>">&nbsp;</span>
                    <span><?php echo nl2br($message->content) ?></span>
                </li>
    <?php endforeach; ?>
            </ul>
		</div>
    </div>
