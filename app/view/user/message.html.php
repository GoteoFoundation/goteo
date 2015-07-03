<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

$user = $vars['user'];
$worthcracy = Worth::getAll();

$_SESSION['msg_token'] = uniqid(rand(), true);

?>
<script type="text/javascript">
	// Mark DOM as javascript-enabled
	jQuery(document).ready(function ($) {
	    //change div#preview content when textarea lost focus
		$("#message").blur(function(){
			$("#preview").html($("#message").val().replace(/\n/g, "<br />"));
		});

		//add fancybox on #a-preview click
		$("#a-preview").fancybox({
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		});
	});
</script>

<?php echo View::get('user/widget/header.html.php', array('user'=>$user)) ?>

<div id="main">

    <div class="center">

    <?php if (!empty($_SESSION['user']->id)) : ?>
    <div class="widget user-message">

        <h3 class="title"><?php echo Text::get('user-message-send_personal-header'); ?></h3>

        <form method="post" action="/message/personal/<?php echo $user->id; ?>">
            <input type="hidden" name="msg_token" value="<?php echo $_SESSION['msg_token'] ; ?>" />

            <label for="contact-subject"><?php echo Text::get('contact-subject-field'); ?></label>
            <input id="contact-subject" type="text" name="subject" value="" placeholder="" />

            <label for="message"><?php echo Text::get('contact-message-field'); ?></label>
            <textarea id="message" name="message" cols="50" rows="5"></textarea>

            <a target="_blank" id="a-preview" href="#preview" class="preview">&middot;<?php echo Text::get('regular-preview'); ?></a>
            <div style="display:none">
                <div id="preview" style="width:400px;height:300px;overflow:auto;">

                    </div>
            </div>



            <button class="green" type="submit"><?php echo Text::get('project-messages-send_message-button'); ?></button>
        </form>

    </div>
    <?php endif; ?>

        <?php echo View::get('user/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>

        <?php echo View::get('user/widget/about.html.php', array('user' => $user)) ?>

        <?php echo View::get('user/widget/social.html.php', array('user' => $user)) ?>

    </div>
    <div class="side">
        <?php echo View::get('user/widget/investors.html.php', $vars) ?>
        <?php echo View::get('user/widget/sharemates.html.php', $vars) ?>
    </div>

</div>

<?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
