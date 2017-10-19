<?php
    $item = $this->raw('item');
    $link = $this->raw('link');

    // print_r($link);
    // print_r($item);
?>
<?php if(is_array($item)): ?>
    <li<?= ($item['id'] === $this->active ? ' class="active"' : '') ?>>
        <?php if(is_array($item['submenu'])): ?>
            <a class="toggle-submenu" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-angle-left hidden-xs"></i> &nbsp;
                <?= $item['text'] ?>
                &nbsp; <i class="fa fa-angle-right visible-xs"></i>
            </a>
            <ul class="nav submenu">
                <li class="visible-xs"><a href="#" class="back"><i class="fa fa-angle-left"></i> <?= $this->text('regular-cancel') ?></a></li>
                <?php foreach($item['submenu'] as $l => $i): ?>
                    <?= $this->insert('partials/header/menu_item', ['link' => $l, 'item' => $i]) ?>
                <?php endforeach ?>
            </ul>
        <?php else: ?>
            <a href="<?= $item['link'] ?>" class="<?= $item['class'] ?>"><?= $item['text'] ?></a>
        <?php endif ?>
    </li>
<?php else: ?>
    <li><a href="<?= $link ?>"><?= $item ?></a></li>
<?php endif ?>
