<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection;

$project = $this['project'];

if (!$project instanceof Model\Project) {
    throw new Redirection('/admin/projects');
}

?>
<div class="widget" >
    <form method="post" action="/admin/projects" >
        <input type="hidden" name="id" value="<?php echo $project->id ?>" />

    <p>
        <label for="open-tag-filter">Cambiar a agrupación:</label><br />
        <select id="open-tag-filter" name="open-tag" >
        <?php print_r($this['open_tags']); ?>
        <?php foreach ($this['open_tags'] as $open_tagId=>$open_tagName) : ?>
            <option value="<?php echo $open_tagId; ?>"<?php if ($project->open_tag == $open_tagId) echo ' selected="selected"';?>><?php echo $open_tagName; ?></option>
        <?php endforeach; ?>
        </select>
    </p>

        <input type="submit" name="save" value="Aplicar" />
    </form>
</div>
