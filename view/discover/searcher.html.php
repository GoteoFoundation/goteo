<?php

use Goteo\Model\Category,
    Goteo\Model\Icon,
    Goteo\Library\Location,
    Goteo\Library\Text;

$categories = Category::getList();  // categorias que se usan en proyectos
$locations = Location::getList();  //localizaciones de royectos
$rewards = Icon::getList(); // iconos que se usan en proyectos

$params = $this['params'];
?>
<div class="widget">
    <h2 class="title"><?php echo Text::get('discover-searcher-header'); ?></h2>

    <form method="post" action="/discover/results">

        <div style="display:block">
            <legend><?php echo Text::get('discover-searcher-bycontent-header'); ?>
                <input type="text" name="query" size="48" value="<?php echo $params['query']; ?>" />
            </legend>
        </div>

        <div style="float:left">
            <label><?php echo Text::get('discover-searcher-bycategory-header'); ?><br />
                <select name="category[]" multiple size="7">
                    <option value="all"<?php if (empty($params['category'])) echo ' selected="selected"'; ?>><?php echo Text::get('discover-searcher-bycategory-all'); ?></option>
                <?php foreach ($categories as $id=>$name) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['category'])) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div style="float:left">
            <label><?php echo Text::get('discover-searcher-bylocation-header'); ?><br />
                <select name="location[]" multiple size="7">
                    <option value="all"<?php if (empty($params['location'])) echo ' selected="selected"'; ?>><?php echo Text::get('discover-searcher-bylocation-all'); ?></option>
                <?php foreach ($locations as $id=>$name) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['location'])) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div style="float:left">
            <label><?php echo Text::get('discover-searcher-byreward-header'); ?><br />
                <select name="reward[]" multiple size="7">
                    <option value="all"<?php if (empty($params['reward'])) echo ' selected="selected"'; ?>><?php echo Text::get('discover-searcher-byreward-all'); ?></option>
                <?php foreach ($rewards as $id=>$reward) : ?>
                    <option value="<?php echo $id; ?>"<?php if (in_array("'{$id}'", $params['reward'])) echo ' selected="selected"'; ?>><?php echo $reward->name; ?></option>
                <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div style="float:left">
            <button type="submit" name="searcher"><?php echo Text::get('discover-searcher-button'); ?></button>
        </div>
        
        <br clear="all" />
    </form>
</div>
