<?php $this->layout('faq/layout') ?>

<?php $this->section('faq-content') ?>

  <!-- Start -- Cesc -->
    <?= $this->insert('faq/partials/header', ['header' => 'home_faqs']) ?>

    <section class="container step1">
      <div class="row">
      	<?php foreach ($this->faq_sections as $section): ?>
      	    <?php if ($section_faq = $section->getFaqs()): ?>
                <article class="col-sm-6 col-lg-3">
                    <div class="faqs_module card">
                        <header class="<?= $section->slug ?>">
                            <h2><?= $section->name ?></h2>
                        </header>
                        <ul>
                            <?php foreach ($section_faq as $faq): ?>
                                <?php $faq_id = $faq->slug ?? $faq->id; ?>
                                <li><a href="<?= '/faq/' . $section->slug . '/' . $faq_id ?>"><?= $faq->title ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <footer class="see_everything">
                            <hr><a href="<?= '/faq/'.$section->slug ?>"><?= $section->button_action ?></a>
                        </footer>
                    </div>
                </article>
          <?php endif; ?>
    	<?php  endforeach; ?>

<?php $this->replace() ?>
