<?php
$messages = $_SESSION['messages'];
unset($_SESSION['messages']);
?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
      jQuery(document).ready(function ($) {
           $(".message-close").click(function (event) {
                    $("#message").fadeOut(2000);
           });
      });
// @license-end
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
