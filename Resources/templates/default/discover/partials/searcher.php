<?php

$rewards = $this->rewards;
$locations = $this->locations;
$categories = $this->categories;
$params = $this->params;

?>
<div class="widget searcher">
    <form method="post" action="/discover/results">
        <div class="text-filter">
            <label for="text-query"><?= $this->text('discover-searcher-bycontent-header') ?></label>
            <input type="text" id="text-query" name="query" size="48" value="<?= $params['query'] ?>" />
            <br clear="all" />
        </div>

        <div class="filter">
            <label for="category"><?= $this->text('discover-searcher-bycategory-header') ?></label>
                <select id="category" name="category[]" multiple size="10">
                    <option class="all" value=""<?php if (empty($params['category'])) echo ' selected="selected"'; ?>><?= $this->text('discover-searcher-bycategory-all') ?></option>
                <?php foreach ($categories as $id => $name) : ?>
                    <option value="<?= $id ?>"<?=(in_array($id, $params['category']) ? ' selected="selected"':'')?>><?= $name ?></option>
                <?php endforeach; ?>
                </select>
        </div>

        <div class="filter">
            <label for="location"><?= $this->text('discover-searcher-bylocation-header') ?></label>
                <select id="location" name="location[]" multiple size="10">
                    <option class="all" value=""<?php if (empty($params['location'])) echo ' selected="selected"'; ?>><?= $this->text('discover-searcher-bylocation-all') ?></option>
                <?php foreach ($locations as $id => $name) : ?>
                    <option value="<?= $id ?>"<?=(in_array($id, $params['location']) ? ' selected="selected"':'')?>><?= $name ?></option>
                <?php endforeach; ?>
                </select>
        </div>

        <div class="filter">
            <label for="reward"><?= $this->text('discover-searcher-byreward-header') ?> </label>
                <select id="reward" name="reward[]" multiple size="10">
                    <option class="all" value=""<?php if (empty($params['reward'])) echo ' selected="selected"'; ?>><?= $this->text('discover-searcher-byreward-all') ?></option>
                <?php foreach ($rewards as $id => $reward) : ?>
                    <option value="<?= $id ?>"<?=(in_array($id, $params['reward']) ? ' selected="selected"':'  ')?>><?php echo $reward->name; ?></option>
                <?php endforeach; ?>
                </select>
        </div>

        <div style="float:left">
            <button type="submit" id="searcher" name= "searcher"><?= $this->text('discover-searcher-button') ?></button>
        </div>

        <br clear="all" />
    </form>
</div>
