<?php
use Goteo\Library\Lang;

$langs = Lang::getAll(true);
// piñonaco para activar portugués en la convocatoria de extremadura
if (isset($call) && $call->id == 'cofinancia-extremadura')
    $langs['pt'] = (object) array('id'=>'pt', 'short'=>'PORT');
?>

    <ul class="lang">
        <?php foreach ($langs as $lang): ?>
            <?php if ($lang->id == LANG) continue; ?>
            <li >
            <a href="?lang=<?php echo $lang->id ?>"><?php echo htmlspecialchars($lang->short) ?></a>
            </li>
        <?php endforeach ?>
    </ul>