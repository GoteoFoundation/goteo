<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Icon;

$labels = $this['searcher'];
$icons = Icon::getAll();
/*
 * Solo poner el pastillo si hay proyectos en esa categoría
 * 
 * 
 *
'promote' => Destacados en portada
'popular' => Misma categoria del discover
'recent'  => Misma categoria del discover
'success' => Misma categoria del discover
'outdate' => Misma categoria del discover
'byreward' => Como búsqueda en el discover (tanto individual como colectivo, supongo)
*/


/*
 * Los elementos visuales son pastillos de toda la linea: letra gris, current en azulito, rolover en negro, ojo triangulo (sprite)
 *
 * El por retorno es diferente
 */
?>
<div class="side_widget">
    <div class="line current button rounded-corners">
        <p><a href="#"><?php echo $labels['promote'] ?></a></p>
    </div>
    <div class="line button  rounded-corners">
        <p><a href="#"><?php echo $labels['popular'] ?></a></p>
    </div>
    <div class="line button  rounded-corners">
        <p><a href="#"><?php echo $labels['recent'] ?></a></p>
    </div>
    <div class="line button rounded-corners">
        <p><a href="#"><?php echo $labels['success'] ?></a></p>
    </div>
    <div class="line button rounded-corners">
        <p><a href="#"><?php echo $labels['outdate'] ?></a></p>
    </div>
    <div class="block rewards rounded-corners">
        <p class="title"><?php echo $labels['byreward'] ?></p>
        <p class="items">
        	<a href="#" class="tipsy file" title="<?php echo $icons['file']->name ?>"><?php echo $icons['file']->name ?></a>
            <a href="#" class="tipsy money" title="<?php echo $icons['money']->name ?>"><?php echo $icons['money']->name ?></a>
            <a href="#" class="tipsy code" title="<?php echo $icons['code']->name ?>"><?php echo $icons['code']->name ?></a>
        	<a href="#" class="tipsy service" title="<?php echo $icons['service']->name ?>"><?php echo $icons['service']->name ?></a>
            <a href="#" class="tipsy manual" title="<?php echo $icons['manual']->name ?>"><?php echo $icons['manual']->name ?></a>
            <a href="#" class="tipsy product" title="<?php echo $icons['product']->name ?>"><?php echo $icons['product']->name ?></a>
            <a href="#" class="tipsy design" title="<?php echo $icons['design']->name ?>"><?php echo $icons['design']->name ?></a>
            <a href="#" class="tipsy other" title="<?php echo $icons['other']->name ?>"><?php echo $icons['other']->name ?></a>
    </div>

</div>
