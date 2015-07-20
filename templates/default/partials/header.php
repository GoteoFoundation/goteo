<?php

use Goteo\Application\Lang;
use Goteo\Library\Currency;

$currencies = Currency::$currencies;

$num_currencies=count($currencies);

$select_currency=Currency::$currencies[$_SESSION['currency']]['html'];

$langs = Lang::listAll('short');

?>
                        <ul class="currency">
                            <?php foreach ($currencies as $ccyId => $ccy): ?>
                                <?php if ($ccyId == $_SESSION['currency']) continue; ?>
                                <li>
                                <a href="?currency=<?php echo $ccyId ?>"><?php echo $ccy['html'].' '.$ccyId; ?></a>
                                </li>
                            <?php endforeach ?>
                        </ul>



                        <ul class="lang">
                            <?php foreach ($langs as $id => $lang): ?>
                                <?php if (Lang::isActive($id)) continue; ?>
                                <li>
                                <a href="?lang=<?php echo $id ?>"><?php echo $lang ?></a>
                                </li>
                            <?php endforeach ?>
                        </ul>

<div id="header">
    <h1><?=$this->text('regular-main-header')?></h1>
    <div id="super-header">
	   <?php include __DIR__ . '/header/highlights.php' ?>

	   <div id="rightside" style="float:right;">
           <div id="about">
                <ul>
                    <li><a href="/about"><?=$this->text('regular-header-about')?></a></li>
                    <li><a href="/blog"><?=$this->text('regular-header-blog')?></a></li>
                    <li><a href="/faq"><?=$this->text('regular-header-faq')?></a></li>
                    <?php if($num_currencies>1) { ?>
                    <li id="currency"><a href="#" ><?php echo $select_currency." ".$_SESSION['currency']; ?></a>

                        <?php // TODO: UL CURRENCY AQUI ?>

                    </li>
                    <?php } ?>
                    <li id="lang"><a href="#" ><?php echo Lang::getShort(Lang::current(true)); ?></a>

                        <?php // TODO: UL LANG AQUI ?>

                    </li>
                </ul>
            </div>


		</div>


    </div>

    <?php include __DIR__ . '/header/menu.php' ?>

</div>

