<?php

use Goteo\Model\Category,
    Goteo\Model\Icon,
    Goteo\Model\Project,
    Goteo\Library\Text;

//activamos la cache
\Goteo\Core\DB::cache(true);

//TODO: esto no deberia ir aqui
$categories = Category::getList();  // categorias que se usan en proyectos
$locations = Project::getProjLocs();  //localizaciones de royectos
$rewards = Icon::getList(); // iconos que se usan en proyectos

$params = $this['params'];
?>
<div class="widget searcher">
    <form method="post" action="/discover/results">
        <div class="text-filter">
            <label for="text-query"><?php echo Text::get('discover-searcher-bycontent-header'); ?></label>
            <input type="text" id="text-query" name="query" size="48" value="<?php echo \htmlspecialchars($params['query']); ?>" />
            <br clear="all" />
        </div>

        <div class="filter">
            <label for="category"><?php echo Text::get('discover-searcher-bycategory-header'); ?></label>
                <select id="category" name="category[]" multiple size="10">
                    <option class="all" value="all"<?php if (empty($params['category'])) echo ' selected="selected"'; ?>><?php echo Text::get('discover-searcher-bycategory-all'); ?></option>
                <?php foreach ($categories as $id=>$name) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['category'])) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
                </select>
        </div>

        <div class="filter">
            <label for="location"><?php echo Text::get('discover-searcher-bylocation-header'); ?></label>
                <select id="location" name="location[]" multiple size="10">
                    <option class="all" value="all"<?php if (empty($params['location'])) echo ' selected="selected"'; ?>><?php echo Text::get('discover-searcher-bylocation-all'); ?></option>
                <?php foreach ($locations as $id=>$name) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['location'])) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
                </select>
        </div>

        <div class="filter">
            <label for="reward"><?php echo Text::get('discover-searcher-byreward-header'); ?> </label>
                <select id="reward" name="reward[]" multiple size="10">
                    <option class="all" value="all"<?php if (empty($params['reward'])) echo ' selected="selected"'; ?>><?php echo Text::get('discover-searcher-byreward-all'); ?></option>
                <?php foreach ($rewards as $id=>$reward) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['reward'])) echo ' selected="selected"'; ?>><?php echo $reward->name; ?></option>
                <?php endforeach; ?>
                </select>
        </div>

        <div style="float:left">
            <button type="submit" id="searcher" name="searcher"><?php echo Text::get('discover-searcher-button'); ?></button>
        </div>

        <br clear="all" />
    </form>
</div>
