<?php
$params = $this['params'];
?>
<div class="widget">
    <h2 class="title">Busca un proyecto</h2>

    <form method="post" action="/discover/results">

        <div style="float:left">
            <label>Por categoria:<br />
                <select name="category[]" multiple size="7">
                    <option value="all"<?php if (empty($params['category'])) echo ' selected="selected"'; ?>>TODAS</option>
                <?php foreach ($this['categories'] as $id=>$name) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['category'])) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div style="float:left">
            <label>Por lugar:<br />
                <select name="location[]" multiple size="7">
                    <option value="all"<?php if (empty($params['location'])) echo ' selected="selected"'; ?>>TODOS</option>
                <?php foreach ($this['locations'] as $id=>$name) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['location'])) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div style="float:left">
            <label>Por retorno:<br />
                <select name="reward[]" multiple size="7">
                    <option value="all"<?php if (empty($params['reward'])) echo ' selected="selected"'; ?>>TODOS</option>
                <?php foreach ($this['rewards'] as $id=>$reward) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['reward'])) echo ' selected="selected"'; ?>><?php echo $reward->name; ?></option>
                <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div style="float:left">
            <button type="submit" name="searcher">Buscar</button>
        </div>
        
        <br clear="all" />
    </form>
</div>
