<?php
use Goteo\Core\View,
 Goteo\Model\Project\Reward,
 Goteo\Model\Invest;

echo new View ('view/dashboard/projects/selector.html.php', $this);

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
    <div class="">
        ESTO ES UNA VISUALIZACIÓN DE LAS OPCIONES DE RETORNO QUE ELIGEN TUS COFINANCIADORES.<br />
        NO TIENES QUE GESTIONAR ESOS RETORNOS HASTA HABER LLEGADO AL MÍNIMO DE LA CANTIDAD DESEADA
    </div>
    <div class="">
        <?php $num = 1;
            foreach ($this['rewards'] as $rewardId=>$rewardData) :
                $who = Invest::choosed($rewardData->id); ?>
            <div style="margin: 20px;">
                (<?php echo $num; ?>) <?php echo $rewardData->amount; ?> &euro; de aporte<br />
                <?php echo count($who); ?> cofinanciadores<br />
                <?php echo $icons[$rewardData->icon]; ?> <?php echo $rewardData->reward; ?><br />
                Recompensa: <?php echo $rewardData->description; ?><br />
                <input type="button" onclick="msgto('<?php echo $rewardData->id; ?>')" value="mensaje a ese grupo"  />
            </div>
        <?php ++$num;
            endforeach; ?>
    </div>
</div>

<div class="widget projects">
    <h2 class="title">Gestionar retornos</h2>
    <div>
        <form id="invests-filter-form" name="filter_form" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/filter'; ?>" method="post">
        <label id="invests-filter">Ver por: </label>
        <select id="invests-filter" name="filter" onchange="document.getElementById('invests-filter-form').submit();">
        <?php foreach ($filters as $filterId=>$filterName) : ?>
            <option value="<?php echo $filterId; ?>"<?php if ($filterId == $this['filter']) echo ' selected="selected"'; ?>><?php echo $filterName; ?></option>
        <?php endforeach; ?>
        </select>
        <!-- un boton para aplicar filtro si no tiene javascript -->
        </form>
    </div>
    <div id="invests-list">
        <form name="invests_form" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/process'; ?>" method="post">
            <input type="hidden" name="filter" value="<?php echo $this['filter']; ?>" />
            <?php foreach ($this['invests'] as $investId=>$investData) :
                $address = $investData->address; ?>
                <div style="margin: 20px;">
                    <?php echo $investData->user->name; ?> <?php echo $investData->amount; ?> &euro;<br />
                    Recompensas esperadas: <br />
                    <?php $cumplida = true; //si nos encontramos una sola no cumplida, pasa a false
                        foreach ($investData->rewards as $reward) :
                            if ($reward->fulfilled != 1) $cumplida = false;
                            ?>
                    <input type="checkbox" id="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>" name="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>" value="1" <?php if ($reward->fulfilled == 1) echo ' checked="checked" disabled';?>  />
                    <label for="ful_reward-<?php echo $investId; ?>-<?php echo $reward->id; ?>"><?php echo $reward->reward; ?></label>
                    <?php endforeach; ?>
                    <br />
                    Direccion: <?php echo $address->address; ?>, <?php echo $address->location; ?>, <?php echo $address->zipcode; ?> <?php echo $address->country; ?><br />
                    <?php echo $cumplida ? 'Cumplida' : 'Pendiente'; ?>
                </div>
            <?php endforeach; ?>

            <input type="submit" name="process" value="Aplicar" class="save" onclick="return confirm('Ojo! Al marcar como cumplida no se puede desmarcar. Continuamos?')"/>
        </form>
    </div>

</div>


<div class="widget projects">
    <a name="message"></a>
    <h2 class="title">Mensajes colectivos</h2>
    <form name="message_form" method="post" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/message'; ?>">
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

        <textarea id="message-users" name="message" cols="50" rows="5"></textarea>
        <input class="button" type="submit" value="Enviar" />
    </form>

</div>

<script type="text/javascript">
    function msgto(reward) {
        document.getElementById('msg_reward-'+reward).checked = 'checked';
        document.location.href = '#message';
        $("#message-users").focus();
    }
</script>