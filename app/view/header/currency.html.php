<?php
use Goteo\Library\Currency;

$currencies = Currency::$currencies;
$current_currency = $currencies[\CURRENCY];
?>
    <ul class="lang">
        <?php foreach ($currencies as $ccyId => $ccy): ?>
            <?php if ($ccyId == \CURRENCY) continue; ?>
            <li >
            <a href="?currency=<?php echo $ccyId ?>"><?php echo $ccy['html'].' '.$ccyId; ?></a>
            </li>
        <?php endforeach ?>
    </ul>