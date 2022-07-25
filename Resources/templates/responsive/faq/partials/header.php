<header id="header_faqs" class="container-fluid <?= $this->faq_section->slug ?? 'home_faqs' ?>">
    <div class="container">
        <div class="row upper_bar">
            <form class="col-sm-6 faq_search" method="GET" action="/faq/search">
                <label>
                    <span class="a-hidden"><?= $this->text('faq-search') ?></span>
                    <input type="search" class="search-field" name="search" value="<?= $this->search ?>" placeholder="<?= $this->t('faq-search') ?>">
                </label>
                <button class="search-submit icon-search"><span class="a-hidden"><?= $this->text('regular-search') ?></span></button>
            </form>
            <div class="col-sm-6 hidden-xs create-project" style="text-align: right;">
                <a href="/project/create" target="_blank" class="btn btn-fashion"><?= $this->text('regular-create') ?></a>
            </div>
        </div>
        <?php if ($this->faq_section): ?>
            <div class="row">
                <h3><a href="/faq"><?= $this->t('faq-title') ?></a></h3>
                <h1><?= $this->faq_section->name ?></h1>
            </div>
        <?php elseif ($this->view == 'search'): ?>
            <div class="row">
                <h1><?= $this->t('faq-search') ?></h1>
            </div>
        <?php else: ?>
            <h1><?= $this->t('faq-title') ?></h1>
        <?php endif; ?>
    </div>
</header>
