<?php
use Goteo\Library\Text;

$page = $this['page'];
?>
<header>
	<nav>
		<a href="<?php echo $page->url; ?>" class="logo<?php if (!$page->home) echo ' page'; ?>"><img src="<?php echo SRC_URL; ?>/view/bazar/img/carro.png" alt="IMG" title="<?php echo htmlspecialchars($page->name); ?>" /></a>
		<?php if ($page->home) : ?>
			<h2><?php echo $page->name; ?></h2>
			<h1><?php echo $page->description; ?></h1>
			<div id="encabezado"><?php echo $page->txt1; ?></div>
		<?php else : ?>
			<h2><a href="<?php echo $page->url; ?>"><?php echo $page->name; ?></a></h2>
			<h1><a href="<?php echo $page->url; ?>"><?php echo $page->description; ?></a></h1>
		<?php endif; ?>
		<img class="logo-goteo" src="<?php echo SRC_URL; ?>/view/bazar/img/logo.svg" alt="Goteo.org" title="Goteo.org" />
	</nav>
</header>
