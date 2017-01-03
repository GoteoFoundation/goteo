<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $vars['project'];
$messengers = $vars['messengers'];

// participantes ordenados por nombre
uasort($messengers, function ($a, $b) {
        if ($a->name == $b->name) return 0;
        return ($a->name > $b->name) ? 1 : -1;
        }
    );

?>
<?php if (!empty($messengers)) : ?>
<div class="widget gestrew">
    <h2 class="title">Usuarios que participan en los mensajes</h2>

    <div id="invests-list">
            <?php foreach ($messengers as $user=>$userMsg) :

                // al impulsor no lo mostramos
                if ($user == $project->owner) continue;
                ?>

                <div class="investor">

                	<div class="left" style="width:50px;">
                        <a href="/user/<?php echo $user; ?>"><img src="<?php echo $userMsg->avatar->getLink(45, 45, true); ?>" /></a>
                    </div>

                    <div class="left" style="width:120px;">
						<span class="username"><a href="/user/<?php echo $user; ?>"><?php echo $userMsg->name; ?></a></span>
                    </div>

                    <div class="left">
                        <span class="profile"><a href="/user/profile/<?php echo $user ?>" target="_blank"><?php echo Text::get('profile-widget-button'); ?></a> </span>
                        <span class="contact"><a onclick="msgto_user('<?php echo $user; ?>', '<?php echo $userMsg->name; ?>')" style="cursor: pointer;"><?php echo Text::get('regular-send_message'); ?></a></span>
                    </div>

                    <div class="left" style="width:540px;">
                        <?php foreach ($userMsg->messages as $msg) : ?>
                        <p><?php if (!empty($msg->thread_text)) {
                            echo '<strong>'.\substr($msg->thread_text, 0, 40).'...:</strong> '.\substr($msg->text, 0, 50).'...';
                        } else {
                            echo '<strong>Inicia hilo:</strong> '.\substr($msg->text, 0, 80).'...';
                        }
                        ?></p>
                        <?php endforeach; ?>
                    </div>


                </div>

            <?php endforeach; ?>
    </div>

</div>

<div class="widget projects" id="colective-messages">
    <a name="message"></a>
    <h2 class="title">Mensaje</h2>

        <form name="message_form" method="post" action="<?php echo '/dashboard/'.$vars['section'].'/'.$vars['option'].'/message'; ?>">
        	<div id="checks">
               <input type="hidden"id="msg_user" name="msg_user" value="" />

                <p>
                    <input type="checkbox" id="msg_all" name="msg_all" value="1" onclick="noindiv(); alert('Masivo a todos los participantes');" />
                    <label for="msg_all">A todos los participantes en mensajes de este proyecto</label>
                </p>

                <div id="msg_user_name"></div>
    		</div>
		    <div id="comment">
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
            <div id="bocadillo"></div>
            <label for="contact-subject"><?php echo Text::get('contact-subject-field'); ?></label>
            <input id="contact-subject" type="text" name="subject" value="" placeholder="" />

            <label for="message"><?php echo Text::get('contact-message-field'); ?></label>
            <textarea rows="5" cols="50" name="message" id="message"></textarea>
            <a class="preview" href="#preview" id="a-preview" target="_blank">&middot;<?php echo Text::get('regular-preview'); ?></a>
            <div style="display:none">
                <div style="width:400px;height:300px;overflow:auto;" id="preview"></div>
            </div>
            <button type="submit" class="green"><?php echo Text::get('project-messages-send_message-button'); ?></button>
            </div>
        </form>

</div>

<?php endif; ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    function noindiv() {
        $('#msg_user').val('');
        $('#msg_user_name').html('');
    }

    function msgto_user(user, name) {
        $('#msg_user').val(user);
        $('#msg_user_name').html('<p>Individual a <strong>'+name+'</strong></p>');
        document.location.href = '#message';
        $("#message").focus();

    }
// @license-end
</script>
