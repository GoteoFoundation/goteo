<?php
use Goteo\Library\Text,
    Goteo\Core\View;
?>
<div class="side_widget summary">
    <div class="line rounded-corners">
    	<p class="text">Total</p>
        <p class="quantity projects">
           	<?php echo \amount_format($this['summary']['projects']) ?><span class="text">proyectos</span>
        </p>
    </div>
    <div class="half rounded-corners">
    	<p class="text">Activos</p>    
        <p class="quantity active"><?php echo \amount_format($this['summary']['active']) ?></p>
    </div>
    <div class="half rounded-corners last">
    	<p class="text">Exitosos</p>        
        <p class="quantity success"><?php echo \amount_format($this['summary']['success']) ?></p>
    </div>
    <div class="half rounded-corners">
    	<p class="text">Cofinanciadores</p>
        <p class="quantity investors"><?php echo \amount_format($this['summary']['investors']) ?></p>
    </div>
    <div class="half rounded-corners last">
    	<p class="text">Colaboradores</p>        
        <p class="quantity supporters"><?php echo \amount_format($this['summary']['supporters']) ?></p>
    </div>
    <div class="line rounded-corners">
    	<p class="text">Dinero recaudado</p>
        <p class="quantity amount violet"><span><?php echo \amount_format($this['summary']['amount']) ?></span><span class="euro">euros</span></p>
    </div>
</div>