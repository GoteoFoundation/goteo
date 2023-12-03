<?php $this->layout('faq/layout', ['title' => $this->faq_section->name]) ?>

<?php $this->section('faq-content') ?>

  <!-- Start -- Cesc -->
    <div id="breadcrumb" class="container-fluid">
	    <div class="container">
			<a href="/faq"><?= $this->t('faq-title') ?></a><span class="slash"> / </span><?= $this->faq_section->name ?>
	    </div>
    </div>

    <?= $this->insert('faq/partials/header', ['header' => $this->faq_section->slug]) ?>

    <section class="container step2">
      	<div class="row">

      		<?php foreach ($this->subsections as $subsection): ?>

	    		<?php if ($faqs = $subsection->getFaqs()): ?>
                    <article class="col-sm-4">
                        <div class="faqs_module">
                            <header><h2><?= $subsection->name ?></h2></header>
                            <ul>
                                <?php foreach($faqs as $faq): ?>

                                    <?php $faq_id= $faq->slug ?? $faq->id; ?>

                                    <li><a href="<?= $this->faq_section->slug.'/'.$faq_id ?>"><?= $faq->title ?></a></li>

                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </article>
                <?php endif; ?>

	    	<?php endforeach; ?>

    	</div>
    	<footer class="unsolved_faq"><a href="/contact" class="btn"><?= $this->t('faq-unsolved-footer') ?></a></footer>

<?php $this->replace() ?>
