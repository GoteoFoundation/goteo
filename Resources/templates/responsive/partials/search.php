<div id="search">
    <button type="button" class="close">×</button>
    <form action="<?= $this->lang_host() ?>discover">
        <input type="search" name="q" value="" placeholder="<?= $this->text('regular-search-desc') ?>" autocomplete="off"/>
        <div class="tooltip"><?= $this->text('regular-search-tooltip') ?></div>
        <button type="submit" class="btn btn-white"><?= $this->text('regular-search') ?></button>
    </form>
</div>
