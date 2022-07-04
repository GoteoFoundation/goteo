<?php $this->layout('faq/layout') ?>

<?php $this->section('faq-content') ?>

    <nav id="breadcrumb" class="container-fluid">
	    <div class="container">
			<a href="/faq"><?= $this->t('faq-title') ?></a><span class="slash"> / </span><a href="<?= '/faq/'.$this->faq_section->slug ?>"><?= $this->faq_section->name ?></a><span class="slash"> / </span><?= $this->faq->title ?>
	    </div>
    </nav>

    <?= $this->insert('faq/partials/header') ?>

    <section class="step3">
      	<div class="row">
		  <aside class="col-sm-3" id="accordion">
	      		<?php foreach ($this->subsections as $subsection): ?>
		    		<?php $faq_subsection = $subsection->getFaqs(); ?>
					<?php if (!empty($faq_subsection)): ?>
						<details <?= $this->faq->subsection_id == $subsection->id? 'open' : '' ?>>
							<summary>
								<h3><?= $subsection->name ?></h3>
							</summary>
							<ul class="description">
								<?php foreach($faq_subsection as $faq): ?>
									<?php if($faq->id==$this->faq->id): ?>
										<li class="select"><?= $faq->title ?></li>
									<?php else: ?>
										<li>
											<a href="<?= '/faq/'.$this->faq_section->slug.'/'.$faq->slug ?>"><?= $faq->title ?></a>
										</li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</details>
					<?php endif; ?>
	      		<?php endforeach; ?>
	      	</aside>
	        <article class="col-sm-8 col-sm-offset-1">
		       	<?= $this->markdown($this->faq->description) ?>
	    		<footer class="unsolved_faq">
		    		<a href="/contact" class="btn"><?= $this->t('faq-unsolved-footer') ?></a>
		    	</footer>
	        </article>
    	</div>
    </section>

<?php $this->replace() ?>
