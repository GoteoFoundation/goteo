<div id="project-selector">
    <form id="selector-form" name="selector_form" action="<?php echo '/dashboard/'.$this['section'].'/'.$this['option'].'/select'; ?>" method="post">
    <?php if (!empty($this['projects'])) : ?>
        <label for="selector">Proyecto:</label>
        <select id="selector" name="project" onchange="document.getElementById('selector-form').submit();">
        <?php foreach ($this['projects'] as $project) : ?>
            <option value="<?php echo $project->id; ?>"<?php if ($project->id == $_SESSION['translate_project']->id) echo ' selected="selected"'; ?>><?php echo $project->name; ?></option>
        <?php endforeach; ?>
        </select>
        <p>El idioma original del proyecto es <strong><?php echo $this['project']->lang_name ?></strong></p>
    <?php else : ?>
        <p>No tienes proyectos, solamente puedes traducir tu perfil.</p>
    <?php endif; ?>
        <label for="selang">Idioma:</label>
        <select id="selang" name="lang" onchange="document.getElementById('selector-form').submit();" style="width:150px;">
        <?php foreach ($this['langs'] as $lng) : ?>
            <option value="<?php echo $lng->id; ?>"<?php if ($lng->id == $_SESSION['translate_project_lang']) echo ' selected="selected"'; ?>><?php echo $lng->name; ?></option>
        <?php endforeach; ?>
        </select>
    </form>
</div>
