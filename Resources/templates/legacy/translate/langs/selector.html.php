<?php
use Goteo\Application\Lang;

$langs = $_SESSION['user']->translangs;

$actual = Lang::get($_SESSION['translate_lang']);
$section = isset($vars['table']) ? $vars['table'] : $vars['section'];
// retorno especial para traduccion de nodo
if (isset($vars['node']) && $vars['node'] != \GOTEO_NODE) {
    $return = '/translate/select/'.$section.'/'.$vars['node'].'/'.$vars['option'];
    if ($vars['option'] == 'data') {
        $return .= '/edit/'.$vars['node'];
    } elseif ($vars['action'] == 'edit_'.$vars['option']) {
        $return .= '/edit';
    }
    if($vars['id']) $return .= '/'.$vars['id'];
} else {
    $return = '/translate/select/'.$section.'/'.$vars['action'];
    if($vars['id']) $return .= '/'.$vars['id'];
    $return .= $vars['filter'].'&page='.$_GET['page'];
}
?>
<div id="lang-selector">
    <form id="selector-form" name="selector_form" action="<?php echo $return; ?>" method="post">
    <?php if (!empty($actual->id)) : ?>
    Estas traduciendo al <strong><?php echo $actual->name ?></strong>. <label for="selector">Cambiar a:</label>
    <?php else : ?>
    No has seleccionado un idioma al que traducir. <label for="selector">Traducir a:</label>
    <?php endif; ?>
    <select id="selector" name="lang" onchange="document.getElementById('selector-form').submit();">
        <option value="" disabled="disabled">Seleccionar idioma de traducci&oacute;n</option>
    <?php foreach ($langs as $langId=>$langName) : ?>
        <option value="<?php echo $langId; ?>"<?php if ($langId == $actual->id) echo ' selected="selected"'; ?>><?php echo $langName; ?></option>
    <?php endforeach; ?>
    </select>
    </form>
</div>
