<?php

use Goteo\Core\View;

$user = $this->user;
$worthcracy = $this->worthcracy;

// TODO: a better way to do this
$_SESSION['msg_token'] = uniqid(rand(), true);

$this->layout('layout', [
    'bodyClass' => 'user-profile',
    ]);

$this->section('content');

?>

<?php echo View::get('user/widget/header.html.php', array('user'=>$user)) ?>

<div id="main">

    <div class="center">

    <?php if ($this->is_logged()) : ?>
    <div class="widget user-message">

        <h3 class="title"><?= $this->text('user-message-send_personal-header') ?></h3>

        <form method="post" action="/message/personal/<?php echo $user->id; ?>">
            <input type="hidden" name="msg_token" value="<?php echo $_SESSION['msg_token'] ; ?>" />

            <label for="contact-subject"><?= $this->text('contact-subject-field') ?></label>
            <input id="contact-subject" type="text" name="subject" value="" placeholder="" />

            <label for="message"><?= $this->text('contact-message-field') ?></label>
            <textarea id="message" name="message" cols="50" rows="5"></textarea>

            <a target="_blank" id="a-preview" href="#preview" class="preview">&middot;<?= $this->text('regular-preview') ?></a>
            <div style="display:none">
                <div id="preview" style="width:400px;height:300px;overflow:auto;">

                    </div>
            </div>



            <button class="green" type="submit"><?= $this->text('project-messages-send_message-button') ?></button>
        </form>

    </div>
    <?php endif; ?>

        <?php echo View::get('user/widget/worth.html.php', array('worthcracy' => $worthcracy, 'level' => $user->worth)) ?>

        <?php echo View::get('user/widget/about.html.php', array('user' => $user)) ?>

        <?php echo View::get('user/widget/social.html.php', array('user' => $user)) ?>

    </div>
    <div class="side">
        <?php echo View::get('user/widget/investors.html.php', $this->vars) ?>
        <?php echo View::get('user/widget/sharemates.html.php', $this->vars) ?>
    </div>

</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    // Mark DOM as javascript-enabled
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

<?php $this->append() ?>
