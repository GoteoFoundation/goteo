<?php $this->layout('faq/layout') ?>

<?php $this->section('faq-content') ?>

  <!-- Start -- Cesc -->
    <header id="header_faqs" class="container-fluid home_faqs">
		<div class="container barra_superior">
			<div class="row">
				<form class="col-sm-6 buscador_faqs">
					<label>
						<span class="a-hidden">¿Qué estás buscando?</span>
						<input type="search" class="search-field" name="" placeholder="¿Qué estás buscando?">
					</label>
					<button class="search-submit icon-search"><span class="a-hidden">Buscar</span></button>
				</form>
				<div class="col-sm-6 hidden-xs" style="text-align: right;">
					<a href="/project/create" target="_blank" class="btn btn-fashion"><?= $this->text('regular-create') ?></a>
				</div>
			</div>
		</div>    
      <h1><?= $this->text('faq-title') ?></h1>
    </header>
    <section class="container pas1">
      <div class="row">

      	<?php foreach ($this->faq_sections as $section): ?>
      	
      	<?php //Get first 3 faq by section ?>
      	<?php $section_faq=$section->getFaqbySection(3); ?>
        <article class="col-sm-6 col-lg-3">
	       	<div class="modul_faqs card">
		       	<header class="<?= $section->slug ?>"><h2><?= $section->name ?></h2></header>
		        <ul>
		        	<?php foreach ($section_faq as $faq): ?>
		        		<?php $faq_id= $faq->slug ? $faq->slug : $faq->id; ?>
			        	<li><a href="<?= $section->slug.'/'.$faq_id ?>"><?= $faq->title ?></a></li>
			    	<?php endforeach; ?>

		        </ul>
		        <footer class="ver_todo">
		        	<hr><a href="<?= '/faq/'.$section->slug ?>"><?= $section->button_action ?></a>
		        </footer>
	       	</div>
        </article>

    	<?php  endforeach; ?>
   
<?php $this->replace() ?>
