<?php
use Goteo\Library\Lang;

$langs = Lang::getAll();
unset($langs['es']); // no se puede traducir a español

$actual = Lang::get($_SESSION['translate_lang']);

$section = isset($this['table']) ? $this['table'] : $this['section'];

?>
<div id="lang-selector">
    <form id="selector-form" name="selector_form" action="<?php echo '/translate/select/'.$section.'/'.$this['action'].'/'.$this['id'].'/'.$this['filter'].'&page='.$_GET['page']; ?>" method="post">
    <?php if (!empty($actual->id)) : ?>
    Estas traduciendo al <strong><?php echo $actual->name ?></strong>. <label for="selector">Cambiar a:</label>
    <?php else : ?>
    No has seleccionado un idioma al que traducir. <label for="selector">Traducir a:</label>
    <?php endif; ?>
    <select id="selector" name="lang" onchange="document.getElementById('selector-form').submit();">
<!--        <option value="">Seleccionar idioma de traducci&oacute;n</option> -->
    <?php foreach ($langs as $lang) : ?>
        <option value="<?php echo $lang->id; ?>"<?php if ($lang->id == $actual->id) echo ' selected="selected"'; ?>><?php echo $lang->name; ?></option>
    <?php endforeach; ?>
    </select>
    </form>
</div>
