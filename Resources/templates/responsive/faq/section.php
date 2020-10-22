<?php $this->layout('faq/layout', ['title' => $faq_section->name]) ?>

<?php $this->section('faq-content') ?>

  <!-- Start -- Cesc -->
    <div id="breadcrumb" class="container-fluid">
	    <div class="container">
			<a href="/faq">FAQs</a><span class="slash"> / </span><?= $this->faq_section->name ?>
	    </div>
    </div>
    <header id="header_faqs" class="container-fluid <?= $this->faq_section->slug?>">
		<div class="container">
			<div class="row barra_superior">
				<form class="col-sm-6 buscador_faqs">
					<label>
						<span class="a-hidden"><?= $this->text('faq-search') ?></span>
						<input type="search" class="search-field" name="" placeholder="¿Qué estás buscando?">
					</label>
					<button class="search-submit icon-search"><span class="a-hidden">Buscar</span></button>
				</form>
				<div class="col-sm-6 hidden-xs" style="text-align: right;">
					<a href="/project/create" target="_blank" class="btn btn-fashion"><?= $this->text('regular-create') ?></a>
				</div>
			</div>
			<div class="row">
				<h3><a href="index.html">FAQs</a></h3>
				<h1><?= $this->faq_section->name ?></h1>
			</div>
		</div>
    </header>
    <section class="container pas2">
      	<div class="row">

      		<?php foreach ($this->subsections as $subsection): ?>
    	
	    		<?php $faq_subsection=$subsection->getFaqbySubsection(5); ?>

		        <article class="col-sm-4">
			       	<div class="modul_faqs">
				       	<header><h2><?= $subsection->name ?></h2></header>
				        <ul>
				        	<?php foreach($faq_subsection as $faq): ?>
				        		
					        	<?php $faq_id= $faq->slug ? $faq->slug : $faq->id; ?>

						        <li><a href="<?= $this->faq_section->slug.'/'.$faq_id ?>"><?= $faq->title ?></a></li>

					    	<?php endforeach; ?>
				        </ul>
			       	</div>
		        </article>

	    	<?php endforeach; ?>

    	</div>
    	<footer class="no_resuelto"><a href="" class="btn">¿NO HEMOS RESUELTO TU DUDA?</a></footer>
   
<?php $this->replace() ?>
