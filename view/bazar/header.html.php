<?php
use Goteo\Library\Text;

$page = $this['page'];
?>
<header>
	<nav>
		<a href="<?php echo $page->url; ?>" class="logo"><img src="/view/bazar/img/carro.png" alt="IMG" title="<?php echo $page->name; ?>"></a>
		<?php if ($page->home) : ?>
			<h1><?php echo $page->name; ?></h1>
			<h2><?php echo $page->description; ?></h2>
			<div id="encabezado"><?php echo $page->txtHome; ?></div>
		<?php else : ?>
			<h1><a href="<?php echo $page->url; ?>"><?php echo $page->name; ?></a></h1>
			<h2><a href="<?php echo $page->url; ?>"><?php echo $page->description; ?></a></h2>
			<div id="encabezado"><?php echo $page->txtHead; ?></div>
		<?php endif; ?>
	</nav>
</header>