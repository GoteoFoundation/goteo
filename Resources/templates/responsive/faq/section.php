<?php $this->layout('faq/layout') ?>

<?php $this->section('faq-content') ?>

  <!-- Start -- Cesc -->
    <div id="breadcrumb" class="container-fluid">
	    <div class="container">
			<a href="index.html">FAQs</a><span class="slash"> / </span>Para Impulsores
	    </div>
    </div>
    <header id="header_faqs" class="container-fluid impulsores">
		<div class="container">
			<div class="row barra_superior">
				<form class="col-sm-6 buscador_faqs">
					<label>
						<span class="a-hidden">¿Qué estás buscando?</span>
						<input type="search" class="search-field" name="" placeholder="¿Qué estás buscando?">
					</label>
					<button class="search-submit icon-search"><span class="a-hidden">Buscar</span></button>
				</form>
				<div class="col-sm-6 hidden-xs" style="text-align: right;"><a href="" class="btn btn-fashion">crea un proyecto</a></div>
			</div>
			<div class="row">
				<h3><a href="index.html">FAQs</a></h3>
				<h1>Para Impulsores</h1>
			</div>
		</div>
    </header>
    <section class="container pas2">
      	<div class="row">
	        <article class="col-sm-4">
		       	<div class="modul_faqs">
			       	<header><h2>Crea tu proyecto</h2></header>
			        <ul>
				        <li><a href="">Què és el finançament col.lectiu i com es planteja aquí?</a></li>
				        <li><a href="">¿Cuándo recibiré el dinero?</a></li>
				        <li><a href="">Quins són els punts forts i diferencials de la plataforma?</a></li>
			        </ul>
		       	</div>
	        </article>
	        <article class="col-sm-4">
		       	<div class="modul_faqs">
			       	<header><h2>Durante la campaña</h2></header>
			        <ul>
				        <li><a href="">Què és el finançament col.lectiu i com es planteja aquí?</a></li>
				        <li><a href="">¿Cuándo recibiré el dinero?</a></li>
				        <li><a href="">Quins són els punts forts i diferencials de la plataforma?</a></li>
			        </ul>
		       	</div>
	        </article>
	        <article class="col-sm-4">
		       	<div class="modul_faqs">
			       	<header><h2>Recibe tu recaudación</h2></header>
			        <ul>
				        <li><a href="">Què és el finançament col.lectiu i com es planteja aquí?</a></li>
				        <li><a href="">¿Cuándo recibiré el dinero?</a></li>
			        </ul>
		       	</div>
	        </article>
    	</div>
    	<footer class="no_resuelto"><a href="" class="btn">¿NO HEMOS RESUELTO TU DUDA?</a></footer>
   
<?php $this->replace() ?>
