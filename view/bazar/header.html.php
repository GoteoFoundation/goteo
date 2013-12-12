<?php
$page = $this['page'];
$item = $this['item'];

$logosrc = (!empty($item->img)) ? $item->imgsrc : '/view/bazar/img/carro.png';
?>
<header>
	<a href="<?php echo $page->url; ?>" class="logo"><img src="<?php echo $logosrc; ?>" alt="IMG" title="<?php echo $page->name; ?>"></a>
	<nav>
		<h1><a href="<?php echo $page->url; ?>"><?php echo $page->name; ?></a></h1>
		<h2><a href="<?php echo $page->url; ?>"><?php echo $page->description; ?></a></h2>
	<?php echo ($page->home) ? '<div id="encabezado">'.$page->content.'</div>' : '<div id="encabezado">'.$item->description.'</div>';  ?>
	</nav>
</header>