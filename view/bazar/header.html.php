<?php
use Goteo\Library\Text;

$page = $this['page'];
?>
<header>
	<nav>
		<a href="<?php echo $page->url; ?>" class="logo<?php if (!$page->home) echo ' page'; ?>"><img src="/view/bazar/img/carro.png" alt="IMG" title="<?php echo $page->name; ?>" /></a>
		<?php if ($page->home) : ?>
			<h1><?php echo $page->name; ?></h1>
			<h2><?php echo $page->description; ?></h2>
			<div id="encabezado"><?php echo $page->txt1; ?></div>
		<?php else : ?>
			<h1><a href="<?php echo $page->url; ?>"><?php echo $page->name; ?></a></h1>
			<h2><a href="<?php echo $page->url; ?>"><?php echo $page->description; ?></a></h2>
		<?php endif; ?>
		<img class="logo-goteo" src="/view/bazar/img/logo.svg" alt="Goteo.org" title="Goteo.org" />
	</nav>
</header>