<?php
$page = $this['page'];
?>
<header>
	<a href="<?php echo $page->url; ?>"><img class="logo" src="/view/bazar/img/carro.png" alt="IMG" title="<?php echo $page->name; ?>"></a>
	<nav>
		<h1><?php echo $page->name; ?></h1>
		<div id="encabezado"><?php echo $page->content; ?></div>
	</nav>
</header>