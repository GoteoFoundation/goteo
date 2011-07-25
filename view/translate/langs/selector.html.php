<?php
use Goteo\Library\Lang;

$langs = Lang::getAll();

$actual = Lang::get($_SESSION['translator_lang']);

?>
<div id="lang-selector">
    <form id="selector-form" name="selector_form" action="<?php echo '/translate/select/'.$this['section'].'/'.$this['option'].'/'.$this['id'].$this['filter']; ?>" method="post">
    Estas traduciendo al <strong><?php echo $actual->name ?></strong>. <label for="selector">Cambiar a:</label>
    <select id="selector" name="lang" onchange="document.getElementById('selector-form').submit();">
    <?php foreach ($langs as $lang) : ?>
        <option value="<?php echo $lang->id; ?>"<?php if ($lang->id == $_SESSION['translator_lang']) echo ' selected="selected"'; ?>><?php echo $lang->name; ?></option>
    <?php endforeach; ?>
    </select>
    </form>
</div>
