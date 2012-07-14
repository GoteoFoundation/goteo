<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$messegers = $this['messegers'];

?>
<?php if (!empty($messegers)) : ?>
<div class="widget gestrew">
    <h2 class="title">Usuarios que participan en los mensajes</h2>
    
    <div id="invests-list">
            <?php foreach ($messegers as $user) :

                // al impulsor no lo mostramos
                if ($user->id == $project->owner) continue;
                ?>
                
                <div class="investor">

                	<div class="left">
                        <a href="/user/<?php echo $user->id; ?>"><img src="<?php echo $user->avatar->getLink(45, 45, true); ?>" /></a>
                    </div>
                    
                    <div class="left" style="width:120px;">
						<span class="username"><a href="/user/<?php echo $user->id; ?>"><?php echo $user->name; ?></a></span>
                    </div>
                   
                    <div class="left">
                        <span class="profile"><a href="/user/profile/<?php echo $user->id ?>" target="_blank"><?php echo Text::get('profile-widget-button'); ?></a> </span>
                        <span class="contact"><a onclick="msgto_user('<?php echo $user->id; ?>', '<?php echo $user->name; ?>')" style="cursor: pointer;"><?php echo Text::get('regular-send_message'); ?></a></span>
                    </div>
                    
                    
                </div>
                
            <?php endforeach; ?>
    </div>

</div>

<div class="widget projects" id="colective-messages">
    <a name="message"></a>
    <h2 class="title">Mensaje</h2>

        <form name="message_form" method="post" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/message'; ?>">
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
            <div id="bocadillo"></div>
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
</script>