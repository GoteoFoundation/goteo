<?php $announcement = $this->announcement; ?>

<div class="grid-donation">
    <a href="/donate/payment?amount=5" class="btn btn-lg btn-white"><?= $this->get_currency() ?> 5</a>
    <a href="/donate/payment?amount=10" class="btn btn-lg btn-white"><?= $this->get_currency() ?> 10</a>
    <a href="/donate/payment?amount=20" class="btn btn-lg btn-white"><?= $this->get_currency() ?> 20</a>
    <a href="/donate/payment?amount=50" class="btn btn-lg btn-white"><?= $this->get_currency() ?> 50</a>
</div>
