<div id="search">
    <button type="button" class="close">×</button>
    <form action="<?= $this->lang_host() ?>discover">
        <input id="input_search" type="search" name="q" value="" placeholder="<?= $this->text('regular-search-desc') ?>" title="<?= $this->text('regular-search-desc') ?>" autocomplete="off"/>
        <label for="input_search" class="tooltip"><?= $this->text('regular-search-tooltip') ?></label>
        <button type="submit" class="btn btn-white"><?= $this->text('regular-search') ?>
    </form>
</div>
