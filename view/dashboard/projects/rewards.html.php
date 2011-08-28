<?php
use Goteo\Core\View,
 Goteo\Model\Project\Reward,
 Goteo\Model\Invest;

$filters = array(
    'date'      => 'Fecha',
    'user'      => 'Usuario',
    'reward'    => 'Recompensa',
    'pending'   => 'Pendientes',
    'fulfilled' => 'Cumplidos'
);

$icons = Reward::icons('individual');

?>
<div class="widget projects">
    <div class="message">
        ESTO ES UNA VISUALIZACIÓN DE LAS OPCIONES DE RETORNO QUE ELIGEN TUS COFINANCIADORES.<br />
        NO TIENES QUE GESTIONAR ESOS RETORNOS HASTA HABER LLEGADO AL MÍNIMO DE LA CANTIDAD DESEADA
    </div>
    <div class="rewards">
        <?php $num = 1; 
            foreach ($this['rewards'] as $rewardId=>$rewardData) :
                $who = Invest::choosed($rewardData->id); ?>
            <div class="reward <?php if(($num % 4)==0)echo " last"?>">
            	<span class="orden">(<?php echo $num; ?>)</span>
                <span class="aporte"><span class="num"><?php echo $rewardData->amount; ?></span> <span class="euro">&nbsp;</span> de aporte</span>
                <span class="cofinanciadores"><span class="num"><?php echo count($who); ?></span> cofinanciadores</span>
                
                <span class="tiporec"><?php echo $icons[$rewardData->icon]; ?> <?php echo $rewardData->reward; ?></span>
                <span class="recompensa"><strong style="color:#666;">Recompensa:</strong><br/> <?php echo $rewardData->description; ?></span>
                <?php if (count($who) > 0) : ?>
                <a class="button green" onclick="msgto('<?php echo $rewardData->id; ?>')" >mensaje a ese grupo</a>
                <?php endif; ?>
            </div>
        <?php ++$num;
            endforeach; ?>
    </div>
</div>

<div class="widget projects">
    <h2 class="title">Gestionar retornos</h2>
    <div class="filters">
        <form id="invests-filter-form" name="filter_form" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/filter'; ?>" method="post">
        <label id="invests-filter">Ver por: </label>
        <ul>			 
        	<li class="current"><a href="#">Por fecha</a></li>
            <li>|</li>
            <li><a href="#">Por orden alfabético</a></li>
            <li>|</li>
            <li><a href="#">Por retorno</a></li>
            <li>|</li>
            <li><a href="#">Pendientes</a></li>
            <li>|</li>
            <li><a href="#">Cumplidas</a></li>
        </ul>
       <!-- <select id="invests-filter" name="filter" onchange="document.getElementById('invests-filter-form').submit();">
        <?php foreach ($filters as $filterId=>$filterName) : ?>
            <option value="<?php echo $filterId; ?>"<?php if ($filterId == $this['filter']) echo ' selected="selected"'; ?>><?php echo $filterName; ?></option>
        <?php endforeach; ?>
        </select>-->
        <!-- un boton para aplicar filtro si no tiene javascript -->
        </form>
    </div>
    
    <div id="invests-list">
        <form name="invests_form" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/process'; ?>" method="post">
            <input type="hidden" name="filter" value="<?php echo $this['filter']; ?>" />
            <?php foreach ($this['invests'] as $investId=>$investData) :
                $address = $investData->address; ?>
                
                <div class="investor">

                	<div class="left">
                        <a href="/user/<?php echo $investData->user->id; ?>"><img src="/image/<?php echo $investData->user->avatar->id; ?>/45/45/1" /></a>
                    </div>
                    
                    <div class="left" style="width:200px;">
						<span class="username"><a href="/user/<?php echo $investData->user->id; ?>"><?php echo $investData->user->name; ?></a></span>
                        <label class="amount">Aporta</label>
						<span class="amount"><?php echo $investData->amount; ?> &euro;</span>
                        <span class="date">22/08/2011</span>
                    </div>
					
                   
                    <div class="left recompensas"  style="width:200px;">
                     	<span style="margin-bottom:2px;">Recompensas esperadas: </span>
                        <?php $cumplida = true; //si nos encontramos una sola no cumplida, pasa a false
                            foreach ($investData->rewards as $reward) :
                                if ($reward->fulfilled != 1) $cumplida = false;
                                ?>
                        <input type="checkbox"  id="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>" name="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>" value="1" <?php if ($reward->fulfilled == 1) echo ' checked="checked" disabled';?>  />
                        <label for="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>"><?php echo $reward->reward; ?></label>
                        <?php endforeach; ?>

                    </div>
                    
					<div class="left" style="width:200px;padding-right:30px;">
                    Direccion: <?php echo $address->address; ?>, <?php echo $address->location; ?>, <?php echo $address->zipcode; ?> <?php echo $address->country; ?>
                    </div>
                    
                    <div class="left">
	                    <span class="status"><?php echo $cumplida ? '<span class="cumplida">Cumplida</span>' : '<span class="pendiente">Pendiente</span>'; ?></span>
                    </div>
                    
                    
                </div>
                
            <?php endforeach; ?>

            <input type="submit" name="process" value="Aplicar" class="save" onclick="return confirm('Ojo! Al marcar como cumplida no se puede desmarcar. Continuamos?')"/>
        </form>
    </div>

</div>


<div class="widget projects" id="colective-messages">
    <a name="message"></a>
    <h2 class="title">Mensajes colectivos</h2>
    
        <form name="message_form" method="post" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/message'; ?>">
        	<div id="checks">
                <input type="hidden" name="filter" value="<?php echo $this['filter']; ?>" />
        
                <p>
                    <input type="checkbox" id="msg_all" name="msg_all" value="1" onclick="alert('a todos es a todos, no tiene en cuenta el resto de marcados');" />
                    <label for="msg_all">A todos los cofinanciadores de este proyecto</label>
                </p>
        
                <p>
                    Por retornos: <br />
                    <?php foreach ($this['rewards'] as $rewardId => $rewardData) : ?>
                        <input type="checkbox" id="msg_reward-<?php echo $rewardId; ?>" name="msg_reward-<?php echo $rewardId; ?>" value="1" />
                        <label for="msg_reward-<?php echo $rewardId; ?>"><?php echo $rewardData->amount; ?> &euro; (<?php echo $rewardData->reward; ?>)</label>
                    <?php endforeach; ?>
        
                </p>
    		</div>
		    <div id="comment">
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
            <div id="bocadillo"></div>
            <textarea rows="5" cols="50" name="message" id="message"></textarea>
            <a class="preview" href="#preview" id="a-preview" target="_blank">·Previsualizar</a>
            <div style="display:none">
                <div style="width:400px;height:300px;overflow:auto;" id="preview"></div>
            </div>
            <button type="submit" class="green">Enviar</button>
            </div>
            
        </form>
	
</div>

<script type="text/javascript">
    function msgto(reward) {
        document.getElementById('msg_reward-'+reward).checked = 'checked';
        document.location.href = '#message';
        $("#message").focus();
    }
</script>