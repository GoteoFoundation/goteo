<?php
use Goteo\Application\Lang;

$langs = Lang::listAll('short');

// piñonaco para activar portugués en la convocatoria de extremadura
// TODO: activar el idioma desde el controlador con Lang::setPublic('pt')
if (isset($call) && $call->id == 'cofinancia-extremadura')
    $langs['pt'] = 'PORT';
?>

    <ul class="lang">
        <?php foreach ($langs as $id => $lang): ?>
            <?php if (Lang::isActive($id)) continue; ?>
            <li >
            <a href="?lang=<?php echo $id ?>"><?php echo htmlspecialchars($lang) ?></a>
            </li>
        <?php endforeach ?>
    </ul>
