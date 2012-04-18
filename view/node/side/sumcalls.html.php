<?php
use Goteo\Library\Text,
    Goteo\Core\View;

// ver página de ver mas convocatorias
?>
<div class="side_widget convocatorias">
    <div class="block rounded-corners">
        <p class="title">Campañas capital riego</p>
        <div style="margin-bottom:6px">
        	<p class="text">Total (todas las campañas)</p>
    	    <p class="quantity all"><span><?php echo $this['sumcalls']['budget'] ?></span><span class="euro">&euro;</span></p>
        </div>
        <div>
	        <p class="text">Queda por repartir</p>
	        <p class="quantity rest"><span><?php echo $this['sumcalls']['rest'] ?></span><span class="euro">&euro;</span></p>
        </div>
    </div>
    <div class="half calls rounded-corners">
        <span><?php echo $this['sumcalls']['calls'] ?></span><br />
        Convocatorias<br />
        Abiertas
    </div>
    <div class="half campaigns rounded-corners last">
        <span><?php echo $this['sumcalls']['campaigns'] ?></span><br />
        Campañas<br />
        Activas
    </div>
</div>

<?php
/*
 * Esto es lo que pone en el elemento central de convocatorias de goteo
 *
     <div class="title">
        <div class="logo"><?php echo Text::get('home-calls-header'); ?></div>
        <?php if (!empty($calls)) : ?>
        <div class="call-count mod1">
            <strong><?php echo count($calls) ?></strong>
            <span>Convocatorias<br />abiertas</span>
        </div>
        <?php endif; ?>

        <?php if (!empty($campaigns)) : ?>
        <div class="call-count mod2">
            <strong><?php echo count($campaigns) ?></strong>
            <span>Campañas<br />activas</span>
        </div>
        <?php endif; ?>
    </div>

*
 *
 */
