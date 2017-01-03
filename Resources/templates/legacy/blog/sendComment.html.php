<?php
	use Goteo\Library\Text,
			Goteo\Model\Blog\Post;
			$allow = Post::allowed($vars['post']);
			$level = (int) $vars['level'] ?: 3;
?>
<?php if ($allow == 1) : ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    jQuery(document).ready(function ($) {
        //change div#preview content when textarea lost focus
        $("#message").blur(function(){
            $("#preview").html($("#message").val().replace(/\n/g, "<br />"));
        });

        //add fancybox on #a-preview click
        $("#a-preview").fancybox({
            'titlePosition'     : 'inside',
            'transitionIn'      : 'none',
            'transitionOut'     : 'none'
        });
    });
// @license-end
</script>
<?php if (!empty($_SESSION['user'])) : ?>
<div class="widget blog-comment">
    <h<?php echo $level ?> class="title"><?php echo Text::get('blog-send_comment-header'); ?></h<?php echo $level ?>>
    <form method="post" action="/message/post/<?php echo $vars['post']; ?><?php echo $vars['project'] ? '/'.$vars['project'] : ''; ?>">
	    <div id="bocadillo"></div>
        <textarea id="message" name="message" cols="50" rows="5"></textarea>
        <a target="_blank" id="a-preview" href="#preview" class="preview">&middot;<?php echo Text::get('regular-preview'); ?></a>
        <div style="display:none">
        	<div id="preview" style="width:400px;height:300px;overflow:auto;">

                </div>
        </div>
        <button class="green" type="submit"><?php echo Text::get('blog-send_comment-button'); ?></button>
    </form>
</div>
<?php endif; ?>
<?php else : ?>
    <p><?php echo Text::get('blog-comments_no_allowed'); ?></p>
<?php endif; ?>
