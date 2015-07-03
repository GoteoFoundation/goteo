<?php
use Goteo\Application\Lang;


// piñonaco para activar portugués en la convocatoria de extremadura
// TODO: por configuracion
if (isset($call) && $call->id == 'cofinancia-extremadura')
    Lang::setPublic('pt');

$langs = Lang::listAll('short');
?>

    <ul class="lang">
        <?php foreach ($langs as $id => $lang): ?>
            <?php if (Lang::isActive($id)) continue; ?>
            <li >
            <a href="?lang=<?php echo $id ?>"><?php echo htmlspecialchars($lang) ?></a>
            </li>
        <?php endforeach ?>
    </ul>
