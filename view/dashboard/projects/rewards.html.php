<?php
use Goteo\Core\View,
    Goteo\Library\Text,
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

$project = $this['project'];

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
            	<div class="orden"><?php echo $num; ?></div>
                <span class="aporte">Aportaciones de <span class="num"><?php echo $rewardData->amount; ?></span> <span class="euro">&nbsp;</span></span>
                <span class="cofinanciadores">cofinanciadores <span class="num"><?php echo count($who); ?></span></span>
                <div class="tiporec"><ul><li class="<?php echo $rewardData->icon; ?>"><?php echo Text::recorta($rewardData->reward, 40); ?></li></ul></div>
                <div class="contenedorrecompensa">	
                	<span class="recompensa"><strong style="color:#666;">Recompensa:</strong><br/> <?php echo Text::recorta($rewardData->description, 100); ?></span>
                </div>
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
                $address = $investData->address;
                $cumplida = true; //si nos encontramos una sola no cumplida, pasa a false
                $estilo = "disabled";
                foreach ($investData->rewards as $reward) {
                    if ($reward->fulfilled != 1) {
                        $estilo = "";
                        $cumplida = false;
                    }
                }
                ?>
                
                <div class="investor">

                	<div class="left">
                        <a href="/user/<?php echo $investData->user->id; ?>"><img src="<?php echo SRC_URL ?>/image/<?php echo $investData->user->avatar->id; ?>/45/45/1" /></a>
                    </div>
                    
                    <div class="left" style="width:120px;">
						<span class="username"><a href="/user/<?php echo $investData->user->id; ?>"><?php echo $investData->user->name; ?></a></span>
                        <label class="amount">Aporta</label>
						<span class="amount"><?php echo $investData->amount; ?> &euro;</span>
                        <span class="date">22/08/2011</span>
                    </div>
                   
                    <div class="left recompensas"  style="width:280px;">
                     	<span style="margin-bottom:2px;" class="<?php echo $estilo;?>"><strong>Recompensas esperadas:</strong></span>
                        <?php foreach ($investData->rewards as $reward) : ?>
                        <div style="width: 250px; overflow: hidden; height: 18px;" class="<?php echo $estilo;?>">
                        <input type="checkbox"  id="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>" name="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>" value="1" <?php if ($reward->fulfilled == 1) echo ' checked="checked" disabled';?>  />
                        <label for="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>"><?php echo Text::recorta($reward->reward, 40); ?></label>
                        </div>
                        <?php endforeach; ?>

                    </div>
                    
					<div class="left" style="width:200px;padding-right:30px;">
						<span style="margin-bottom:2px;" class="<?php echo $estilo;?>"><strong>Direcci&oacute;n de entrega: </strong></span>
						<span class="<?php echo $estilo;?>">
                    	<?php echo $address->address; ?>, <?php echo $address->location; ?>, <?php echo $address->zipcode; ?> <?php echo $address->country; ?>
                    	</span>
                    </div>
                    
                    <div class="left">
	                    <span class="status"><?php echo $cumplida ? '<span class="cumplida">Cumplida</span>' : '<span class="pendiente">Pendiente</span>'; ?></span>
                    </div>
                    
                    
                </div>
                
            <?php endforeach; ?>

            <?php if ($project->amount >= $project->mincost) : ?>
            <input type="submit" name="process" value="Aplicar" class="save" onclick="return confirm('Ojo! Al marcar como cumplida no se puede desmarcar. Continuamos?')"/>
            <?php endif; ?>
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
                        <label for="msg_reward-<?php echo $rewardId; ?>"><?php echo $rewardData->amount; ?> &euro; (<?php echo Text::recorta($rewardData->reward, 40); ?>)</label>
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