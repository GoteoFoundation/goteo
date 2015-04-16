<?php
use Goteo\Application\Lang;

$langs = $_SESSION['user']->translangs;
unset($langs['es']); // no se puede traducir a español

$actual = Lang::get($_SESSION['translate_lang']);

$section = isset($this['table']) ? $this['table'] : $this['section'];
// retorno especial para traduccion de nodo
if (isset($this['node']) && $this['node'] != \GOTEO_NODE) {
    $return = '/translate/select/'.$section.'/'.$this['node'].'/'.$this['option'];
    if ($this['option'] == 'data') {
        $return .= '/edit/'.$this['node'];
    } else if ($this['action'] == 'edit_'.$this['option']) {
        $return .= '/edit/'.$this['id'];
    } else {
        $return .= '/'.$this['id'];
    }
} else {
    $return = '/translate/select/'.$section.'/'.$this['action'].'/'.$this['id'].'/'.$this['filter'].'&page='.$_GET['page'];
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
