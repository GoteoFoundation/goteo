<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project\Reward,
    Goteo\Model\Invest;

$icons = Reward::icons('individual');

$project = $vars['project'];

$rewards = $vars['rewards'];
// recompensas ordenadas por importe
uasort($rewards, function ($a, $b) {
        if ($a->amount == $b->amount) return 0;
        return ($a->amount > $b->amount) ? 1 : -1;
        }
    );

$invests = $vars['invests'];

$filter = $vars['filter']; // al ir mostrando, quitamos los que no cumplan
// pending = solo los que tienen alguna recompensa pendientes
// fulfilled = solo los que tienen todas las recompensas cumplidas
// resign = solo los que hicieron renuncia a recompensa

$order = $vars['order'];
// segun order:
switch ($order) {
    case 'date': // fecha aporte, mas reciente primero
        uasort($invests, function ($a, $b) {
                if ($a->invested == $b->invested) return 0;
                return ($a->invested > $b->invested) ? -1 : 1;
                }
            );
        break;
    case 'user': // nombre de usuario alfabetico
        uasort($invests, function ($a, $b) {
                if ($a->user->name == $b->user->name) return 0;
                return ($a->user->name > $b->user->name) ? 1 : -1;
                }
            );
        break;
    case 'reward': // importe de recompensa, más bajo primero
        uasort($invests, function ($a, $b) {
                if (empty($a->rewards)) return 1;
                if (empty($b->rewards)) return -1;
                if ($a->rewards[0]->amount == $b->rewards[0]->amount) return 0;
                return ($a->rewards[0]->amount > $b->rewards[0]->amount) ? 1 : -1;
                }
            );
        break;
    case 'amount': // importe aporte, más alto primero
    default:
        uasort($invests, function ($a, $b) {
                if ($a->amount == $b->amount) return 0;
                return ($a->amount > $b->amount) ? -1 : 1;
                }
            );
        break;
}

?>
<div class="widget gestrew">
    <div class="message"><?php echo (in_array($project->status, array(4, 5))) ? Text::html('dashboard-rewards-investors_table') : Text::html('dashboard-rewards-notice'); ?></div>
    <div class="rewards">
        <?php $num = 1;
            foreach ($rewards as $rewardId=>$rewardData) : ?>
            <div class="reward <?php if(($num % 4)==0)echo " last"?>">
            	<div class="orden"><?php echo $num; ?></div>
                <span class="aporte"><?php echo Text::get('dashboard-rewards-amount_group') ?><span class="num"><?php echo $rewardData->amount; ?></span></span>
                <span class="cofinanciadores"><?php echo Text::get('dashboard-rewards-num_taken') ?><span class="num"><?php echo $rewardData->getTaken(); ?></span></span>
                <div class="tiporec"><ul><li class="<?php echo $rewardData->icon; ?>"><?php echo Text::recorta($rewardData->reward, 40); ?></li></ul></div>
                <div class="contenedorrecompensa">
                	<span class="recompensa"><strong style="color:#666;"><?php echo Text::get('rewards-field-individual_reward-reward') ?></strong><br/> <?php echo Text::recorta($rewardData->description, 100); ?></span>
                </div>
                <a class="button green" onclick="msgto('<?php echo $rewardData->id; ?>')" ><?php echo Text::get('dashboard-rewards-group_msg') ?></a>
            </div>
        <?php ++$num;
            endforeach; ?>
    </div>
</div>

<?php if (!empty($invests)) : ?>
<script type="text/javascript">
    function set(what, which) {
        document.getElementById('invests-'+what).value = which;
        document.getElementById('invests-filter-form').submit();
        return false;
    }
</script>
<div class="widget gestrew">
    <h2 class="title">Gestionar retornos</h2>
    <a name="gestrew"></a>
   <form id="invests-filter-form" name="filter_form" action="<?php echo '/dashboard/projects/rewards/filter#gestrew'; ?>" method="post">
       <input type="hidden" id="invests-filter" name="filter" value="<?php echo $filter; ?>" />
       <input type="hidden" id="invests-order" name="order" value="<?php echo $order; ?>" />
   </form>
    <div class="filters">
        <label>Ver aportaciones: </label><br />
        <ul>
        	<li<?php if ($order == 'amount' || $order == '') echo ' class="current"'; ?>><a href="#" onclick="return set('order', 'amount');"><?php echo Text::get('dashboard-rewards-amount_order'); ?></a></li>
            <li>|</li>
        	<li<?php if ($order == 'date') echo ' class="current"'; ?>><a href="#" onclick="return set('order', 'date');"><?php echo Text::get('dashboard-rewards-date_order'); ?></a></li>
            <li>|</li>
            <li<?php if ($order == 'user') echo ' class="current"'; ?>><a href="#" onclick="return set('order', 'user');"><?php echo Text::get('dashboard-rewards-user_order'); ?></a></li>
            <li>|</li>
            <li<?php if ($order == 'reward') echo ' class="current"'; ?>><a href="#" onclick="return set('order', 'reward');"><?php echo Text::get('dashboard-rewards-reward_order'); ?></a></li>
            <li>|</li>
            <li<?php if ($filter == 'pending') echo ' class="current"'; ?>><a href="#" onclick="return set('filter', 'pending');"><?php echo Text::get('dashboard-rewards-pending_filter'); ?></a></li>
            <li>|</li>
            <li<?php if ($filter == 'fulfilled') echo ' class="current"'; ?>><a href="#" onclick="return set('filter', 'fulfilled');"><?php echo Text::get('dashboard-rewards-fulfilled_filter'); ?></a></li>
            <li>|</li>
            <li<?php if ($filter == 'resign') echo ' class="current"'; ?>><a href="#" onclick="return set('filter', 'resign');"><?php echo Text::get('dashboard-rewards-resign_filter'); ?></a></li>
            <li>|</li>
            <li<?php if ($filter == '') echo ' class="current"'; ?>><a href="#" onclick="return set('filter', '');"><?php echo Text::get('dashboard-rewards-all_filter'); ?></a></li>
        </ul>
    </div>

    <div id="invests-list">
        <form name="invests_form" action="<?php echo '/dashboard/'.$vars['section'].'/'.$vars['option'].'/process'; ?>" method="post">
           <input type="hidden" name="filter" value="<?php echo $filter; ?>" />
           <input type="hidden" name="order" value="<?php echo $order; ?>" />
            <?php foreach ($invests as $investId=>$investData) :

                $address = $investData->address;
                $cumplida = true; //si nos encontramos una sola no cumplida, pasa a false
                $estilo = " disabled";
                foreach ($investData->rewards as $reward) {
                    if ($reward->fulfilled != 1) {
                        $estilo = "";
                        $cumplida = false;
                    }
                }

                // filtro
                if ($filter == 'pending' && ($cumplida != false || empty($investData->rewards))) continue;
                if ($filter == 'fulfilled' && ($cumplida != true)) continue;
                if ($filter == 'resign' && !$investData->resign) continue;
                if ($order  == 'reward' && empty($investData->rewards)) continue;
                ?>

                <div class="investor">

                    <div class="left" style="width:45px;">
                        <a href="/user/<?php echo $investData->user->id; ?>"><img src="<?php echo $investData->user->avatar->getLink(45, 45, true); ?>" /></a>
                    </div>

                    <div class="left" style="width:120px;">
						<span class="username"><a href="/user/<?php echo $investData->user->id; ?>"><?php echo $investData->user->name; ?></a></span>
                        <label class="amount">Aporte<?php if ($investData->anonymous) echo ' <strong>'.  Text::get('regular-anonymous').'</strong>'; echo " [{$investData->id}]";?></label>
						<span class="amount"><?php echo $investData->amount; ?> &euro;</span>
                        <span class="date"><?php echo date('d-m-Y', strtotime($investData->invested)); ?></span>
                    </div>

                    <div class="left recompensas"  style="width:280px;">
                     	<span style="margin-bottom:2px;" class="<?php echo 'dis'.$investId; echo $estilo;?>"><strong><?php echo Text::get('dashboard-rewards-choosen'); ?></strong></span>
                    <?php if (empty($investData->rewards)) : // si no hay recompensas es renuncia ?>
                     	<span style="margin-bottom:2px;"><strong><?php echo Text::get('dashboard-rewards-resigns'); ?></strong></span>
                    <?php else : ?>
                        <?php foreach ($investData->rewards as $reward) : ?>
                        <div style="width: 250px; overflow: hidden; height: 18px; margin-bottom:2px;" class="<?php echo 'dis'.$investId; echo $estilo;?>">
                        <?php if (in_array($project->status, array(4,5))) : ?>
                            <input type="checkbox" class="fulrew" id="<?php echo "ful_reward-{$investId}-{$reward->id}"; ?>" ref="<?php echo $investId; ?>" value="1" <?php if ($reward->fulfilled == 1) echo ' checked="checked" disabled';?>  />
                            <label for="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>"><?php echo Text::recorta($reward->reward, 100); ?></label>
                        <?php else : ?>
                            <span><?php echo Text::recorta($reward->reward, 100); ?></span>
                        <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (in_array($project->status, array(4,5)) && !empty($investData->rewards) ) : ?>
                        <span class="status" id="status-<?php echo $investId; ?>"><?php echo $cumplida ? '<span class="cumplida">'.Text::get('dashboard-rewards-fulfilled_status').'</span>' : '<span class="pendiente">'.Text::get('dashboard-rewards-pending_status').'</span>'; ?></span>
                    <?php endif; ?>
                    <?php if ($investData->issue) : // si es incidencia ?>
                     	<span style="margin-bottom:2px;color:red;"><strong><?php echo Text::get('dashboard-rewards-issue'); ?></strong></span>
                    <?php endif; ?>
                    </div>

					<div class="left" style="width:200px;padding-right:30px;">
						<span style="margin-bottom:2px;" class="<?php echo 'dis'.$investId; echo $estilo;?>"><strong><?php echo Text::get('dashboard-rewards-address'); ?></strong></span>
						<span class="<?php echo 'dis'.$investId; echo $estilo;?>">
                    	<?php echo $address->address; ?>, <?php echo $address->location; ?>, <?php echo $address->zipcode; ?> <?php echo $address->country; ?>
                    	</span>
                    <?php if ($investData->resign) : // donativo o reconocimiento/agradecimiento ?>
                     	<span style="margin-bottom:2px;color:green;"><?php echo Text::get('dashboard-rewards-thanks'); ?></span>
                    <?php endif; ?>
                    </div>

                    <div class="left">
                        <span class="profile"><a href="/user/profile/<?php echo $investData->user->id ?>" target="_blank"><?php echo Text::get('profile-widget-button'); ?></a> </span>
                        <span class="contact"><a onclick="msgto_user('<?php echo $investData->user->id; ?>', '<?php echo addslashes($investData->user->name); ?>')" style="cursor: pointer;"><?php echo Text::get('regular-send_message'); ?></a></span>
                    </div>


                </div>

            <?php endforeach; ?>

        </form>
    </div>

</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {

        // al clickar, aviso
        $("#sacaexcel").click(function(){
            alert('<?php echo Text::get('dashboard-investors_table-disclaimer'); ?>');
        });

        // al clickar el recuadro de recompensa
        $(".fulrew").click(function(){

            // solo si el proyecto está financiado
            <?php if (!in_array($project->status, array(4,5))) : ?>
            alert('No tienes que marcar las recompensas cumplidas hasta que el proyecto termine la campaña');
            $(this).attr('checked', false);
            return false;
            <?php endif; ?>

            // confirmación porque no se puede desacer
            if (confirm('<?php echo Text::get('dashboard-rewards-process_alert'); ?>')) {
                // usar webservice para marcar como cumplida
                success_text = $.ajax({async: false, type: "POST", data: ({token: $(this).attr('id')}), url: '<?php echo SITE_URL; ?>/ws/fulfill_reward/<?php echo $project->id; ?>/<?php echo $_SESSION['user']->id; ?>'}).responseText;
                if (success_text == '') {
                    $(this).attr('checked', false);
                    alert('Ha habido algún problema, contáctanos');
                } else {
                    $(this).attr('disabled', 'disabled');
                    $('#status-'+$(this).attr('ref')).html('<span class="cumplida"><?php echo Text::get('dashboard-rewards-fulfilled_status'); ?></span>')
                    $('.dis'+$(this).attr('ref')).addClass('disabled');
                }
            } else {
                $(this).attr('checked', false);
            }
        });
    });
</script>


<div class="widget projects" id="colective-messages">
    <a name="message"></a>
    <h2 class="title"><?php echo Text::get('dashboard-rewards-massive_msg'); ?></h2>

        <form name="message_form" method="post" action="<?php echo '/dashboard/'.$vars['section'].'/'.$vars['option'].'/message'; ?>">
        	<div id="checks">
               <input type="hidden" name="filter" value="<?php echo $filter; ?>" />
               <input type="hidden" name="order" value="<?php echo $order; ?>" />
               <input type="hidden"id="msg_user" name="msg_user" value="" />

                <p>
                    <input type="checkbox" id="msg_all" name="msg_all" value="1" onclick="noindiv(); alert('<?php echo Text::get('dashboard-rewards-massive_msg-all_alert'); ?>');" />
                    <label for="msg_all"><?php echo Text::get('dashboard-rewards-massive_msg-all'); ?></label>
                </p>

                <p>
                    Por retornos: <br />
                    <?php foreach ($rewards as $rewardId => $rewardData) : ?>
                        <input type="checkbox" id="msg_reward-<?php echo $rewardId; ?>" name="msg_reward-<?php echo $rewardId; ?>" value="1" onclick="noindiv();"/>
                        <label for="msg_reward-<?php echo $rewardId; ?>"><?php echo $rewardData->amount; ?> &euro; (<?php echo Text::recorta($rewardData->reward, 40); ?>)</label>
                    <?php endforeach; ?>

                </p>

                <div id="msg_user_name"></div>
    		</div>
		    <div id="comment">
            <script type="text/javascript">
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
    function noindiv() {
        $('#msg_user').val('');
        $('#msg_user_name').html('');
        $('#msg_all').val('1');
    }
    function msgto(reward) {
        noindiv();
        document.getElementById('msg_reward-'+reward).checked = 'checked';
        document.location.href = '#message';
        $("#message").focus();
    }

    function msgto_user(user, name) {
        document.getElementById('msg_all').checked = '';
        $('#msg_all').val('');

        $('#msg_user').val(user);
        $('#msg_user_name').html('<p><?php echo Text::get('dashboard-rewards-user_msg'); ?><strong>'+name+'</strong></p>');
        document.location.href = '#message';
        $("#message").focus();

    }
</script>
