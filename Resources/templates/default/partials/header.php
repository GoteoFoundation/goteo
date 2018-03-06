<?php

use Goteo\Application\Currency;

$currencies = Currency::$currencies;

$num_currencies=count($currencies);

$langs = $this->lang_list('short');

?>
                        <ul class="currency">
                            <?php foreach ($currencies as $ccyId => $ccy): ?>
                                <?php if ($ccyId == $this->get_currency('id')) continue; ?>
                                <li>
                                <a href="?currency=<?= $ccyId ?>"><?= $ccy['html'].' '.$ccyId ?></a>
                                </li>
                            <?php endforeach ?>
                        </ul>



                        <ul class="lang">
                            <?php foreach ($langs as $id => $lang): ?>
                                <?php if ($this->lang_active($id)) continue; ?>
                                <li>
                                <a href="<?= $this->lang_url($id) ?>"><?= $lang ?></a>
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
                    <li id="currency"><a href="#" ><?= $this->get_currency()." ".$this->get_currency('id') ?></a>

                        <?php // TODO: UL CURRENCY AQUI ?>

                    </li>
                    <?php } ?>
                    <li id="lang"><a href="#" ><?= $this->lang_short() ?></a>

                        <?php // TODO: UL LANG AQUI ?>

                    </li>
                </ul>
            </div>


		</div>


    </div>

    <?php include __DIR__ . '/header/menu.php' ?>

</div>

