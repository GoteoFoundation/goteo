<?php
use Goteo\Application\Currency;

$currencies = Currency::$currencies;

$num_currencies=count($currencies);

$select_currency=Currency::$currencies[$_SESSION['currency']]['html'];

?>
    <ul class="currency">
        <?php foreach ($currencies as $ccyId => $ccy): ?>
            <?php if ($ccyId == $_SESSION['currency']) continue; ?>
            <li >
            <a href="?currency=<?php echo $ccyId ?>"><?php echo $ccy['html'].' '.$ccyId; ?></a>
            </li>
        <?php endforeach ?>
    </ul>
