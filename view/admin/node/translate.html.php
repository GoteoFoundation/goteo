<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\NormalForm;

$node = $this['node'];
$nodeLang = $this['nodeLang'];

if (!$node instanceof Model\Node) {
    throw new Redirection('/admin');
}
?>
<div class="widget board">
    <form id="lang-form" name="lang_form" action="/admin/node/lang" method="post">
        <label for="selang">Traduciendo los datos del nodo al:</label>
        <select id="selang" name="lang" onchange="document.getElementById('lang-form').submit();" style="width:150px;">
        <?php foreach ($this['langs'] as $lng) : ?>
            <option value="<?php echo $lng->id; ?>"<?php if ($lng->id == $_SESSION['translate_lang']) echo ' selected="selected"'; ?>><?php echo $lng->name; ?></option>
        <?php endforeach; ?>
        </select>
    </form>

    <form method="post" action="/admin/node/translate">
        <input type="hidden" name="lang" value="<?php echo $_SESSION['translate_lang'] ?>" />

        <p>
            <label>Título<br />
            <input type="text" name="subtitle" value="<?php echo $nodeLang->subtitle ?>" style="width:350px" />
            </label>
        </p>

        <p>
            <label>Presentación<br />
            <textarea name="description" cols="100" rows="10"><?php echo $nodeLang->description; ?></textarea><br />
            </label>
        </p>

        <input type="submit" name="save-lang" value="Aplicar los cambios" />
    </form>
</div>


<div class="widget board">
    <h3>Datos originales</h3>

    <table>
        <tr>
            <td>Título</td>
            <td><?php echo $node->subtitle ?></td>
        </tr>
        <tr>
            <td>Presentación</td>
            <td><?php echo $node->description ?></td>
        </tr>
    </table>


</div>
