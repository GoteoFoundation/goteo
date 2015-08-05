<?php
    use Goteo\Library\Text,
        Goteo\Application\Lang,
        Goteo\Application\Config,
        Goteo\Model\Node,
        Goteo\Model\Banner;

$nodeData = Node::get(Config::get('current_node'), LANG);
$banners = Banner::getAll(true, Config::get('current_node'));

$nodeText = str_replace(array('[', ']'), array('<span class="blue">', '</span>'), $nodeData->description);
?>
<?php include __DIR__ . '/../header/lang.html.php' ?>
<div id="header">
    <h1><?php echo Text::get('regular-main-header'); ?></h1>
    <div id="super-header">
		<div id="goteo-logo">
			<ul>
				<li class="home"><a class="node-jump" href="<?php echo GOTEO_URL ?>">Inicio</a></li>
			</ul>
		</div>

	   <div id="rightside" style="float:right;">
           <div id="about">
                <ul>
                    <li><a href="/about"><?php echo str_replace('Goteo', $nodeData->name, Text::get('regular-header-about')); ?></a></li>
                    <li><a href="/blog"><?php echo Text::get('regular-header-blog'); ?></a></li>
                    <li><a href="/faq"><?php echo Text::get('regular-header-faq'); ?></a></li>
                    <li id="lang"><a href="#" ><?php echo Lang::getShort(Lang::current(true)); ?></a></li>
                </ul>
            </div>

		</div>

    </div>

    <div id="node-header">
        <div class="logos">
            <div class="node-home"><a href="<?php echo $nodeData->url ?>"><?php echo $nodeData->name ?></a></div>
            <div class="node-intro"><?php echo $nodeText; ?></div>
            <?php if ($nodeData->logo instanceof \Goteo\Model\Image) : ?>
            <div class="node-logo">
                <span><?php echo Text::get('node-header-sponsorby'); ?></span>
                <img src="<?php echo $nodeData->logo->getLink(150, 75) ?>" alt="<?php echo htmlspecialchars($nodeData->subtitle) ?>" />
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include __DIR__ . '/../node/menu.html.php' ?>
    <?php include __DIR__ . '/../node/banners.html.php' ?>
</div>
