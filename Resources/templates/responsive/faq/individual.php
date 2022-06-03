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
	        <article class="col-sm-8 col-sm-offset-1 col-sm-push-3">
		       	<?= $this->markdown($this->faq->description) ?>
	    		<footer class="unsolved_faq">
		    		<a href="/contact" class="btn"><?= $this->t('faq-unsolved-footer') ?></a>
		    	</footer>
	        </article>
	        <aside class="col-sm-3 col-sm-pull-9" id="accordion">
	      		<?php foreach ($this->subsections as $subsection): ?>
		    		<?php $faq_subsection = $subsection->getFaqs(); ?>
		      		<section>
			      		<h3 role="button" data-toggle="collapse" href="<?= '#collapse-'.$subsection->id ?>" aria-expanded="<?= $this->faq->subsection_id==$subsection->id ? 'true' : 'false' ?>">
			      			<?= $subsection->name ?>
			      		</h3>
			      		<ul class="description collapse <?= $this->faq->subsection_id==$subsection->id ? 'in' : 'false' ?>" id="<?= 'collapse-'.$subsection->id ?>">
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
		      		</section>
	      		<?php endforeach; ?>
	      	</aside>
    	</div>
    </section>

<?php $this->replace() ?>
