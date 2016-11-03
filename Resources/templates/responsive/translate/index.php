<?php

$this->layout("translate/layout");


$this->section('translate-content');

$fields = $this->fields;
$q = $this->get_query('q');
$query = $this->get_query() ? '?'. http_build_query($this->get_query()) : '';


?>
        <h3><?= $this->text('regular-recent-activity') ?></h3>

        <ul class="list-group">
            <?php foreach ($this->feed as $item): ?>
                <li class="list-group-item">
                    <span class="badge"><?= $this->text('feed-timeago', $item->timeago) ?></span>
                    <?php echo $item->html; ?>
                </li>
            <?php endforeach ?>
        </ul>

<div class="well">
    <h3><?= $this->text('translator-choose') ?></h3>
    <dl>
        <?php foreach($this->zones as $k => $parts) :?>
            <dt>
                <?= $this->text("translator-$k") ?>
            </dt>
            <dd>
                <ul class="list-unstyled">
                <?php foreach($parts as $v) :?>
                    <li><a href="/translate/<?= $v ?>"><?= $this->text("translator-$v") ?></a></li>
                <?php endforeach ?>
                </ul>
            </dd>
        <?php endforeach ?>
    </dl>

</div>
<?php $this->replace() ?>
