<?php $this->layout('faq/layout') ?>

<?php $this->section('faq-content') ?>

  <div id="breadcrumb" class="container-fluid">
	    <div class="container">
			<a href="/faq">FAQs</a><span class="slash"> / </span><a href="<?= '/faq/'.$this->faq_section->slug ?>"><?= $this->faq_section->name ?></a><span class="slash"> / </span><?= $this->faq->title ?>
	    </div>
    </div>
    <header id="header_faqs" class="container-fluid donors">
		<div class="container">
			<div class="row barra_superior">
				<form class="col-sm-6 buscador_faqs">
					<label>
						<span class="a-hidden">¿Qué estás buscando?</span>
						<input type="search" class="search-field" name="" placeholder="¿Qué estás buscando?">
					</label>
					<button class="search-submit icon-search"><span class="a-hidden"><?= $this->text('regular-search') ?></span></button>
				</form>
				<div class="col-sm-6 hidden-xs" style="text-align: right;">
					<a href="/project/create" target="_blank" class="btn btn-fashion"><?= $this->text('regular-create') ?></a>
				</div>                
			</div>
			<div class="row">
				<h3><a href="pas2_donantes.htm">Para Donantes</a></h3>
				<h1><?= $this->faq->title ?></h1>
			</div>
		</div>
    </header>
    <section class="container pas3">
      	<div class="row">
	        <article class="col-sm-8 col-sm-offset-1 col-sm-push-3">
		       	<?= $this->faq->description ?>
	    		<footer class="no_resuelto">
		    		<a href="/contact" class="btn">¿NO HEMOS RESUELTO TU DUDA?</a>
		    	</footer>
	        </article>
	        <aside class="col-sm-3 col-sm-pull-9">
	      		<h2 class="otras_preguntas">Otras preguntas para Donantes</h2>
	      		<section>
		      		<h3 class="open">Crea tu proyecto</h3>
		      		<ul>
				        <li><a href="">Què és el finançament col.lectiu i com es planteja aquí?</a></li>
				        <li class="select">¿Es mi proyecto apto?</li>
				        <li><a href="">Quins són els punts forts i diferencials de la plataforma?</a></li>
			        </ul>
	      		</section>
	      		<section>
			        <h3>Durante la campaña</h3>
			        <!--<ul>
				        <li><a href="">Què és el finançament col.lectiu i com es planteja aquí?</a></li>
				        <li><a href="">¿Cuándo recibiré el dinero?</a></li>
				        <li><a href="">Quins són els punts forts i diferencials de la plataforma?</a></li>
			        </ul>-->
		        </section>
		        <section>
			        <h3 class="open">Recibe tu recaudación</h3>
			        <ul>
				        <li><a href="">Què és el finançament col.lectiu i com es planteja aquí?</a></li>
				        <li><a href="">¿Cuándo recibiré el dinero?</a></li>
			        </ul>
		        </section>
	      	</aside>
    	</div>
    </section>
   
<?php $this->replace() ?>
