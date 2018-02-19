<?php
    $item = $this->raw('item');
    $link = $this->raw('link');

    // print_r($link);
    // print_r($item);
?>
<?php
    if(is_array($item)):
        $class = explode(' ', $item['class']);
        $submenu = $item['submenu'];
        if($item['id'] === $this->active) {
            $class[] = 'active';
        } else {
            if(is_array($submenu)) {
                if(in_array($this->active, array_column($submenu, 'id'))) {
                    $class[] = 'active';
                    $class[] = 'selected';
                }
            }
        }
?>
    <li<?= ( $class ? ' class="' . implode(' ', $class) . '"' : '') ?> id="menu-item-<?= $item['id'] ?>">
        <?php if(is_array($submenu)): ?>
            <a class="toggle-submenu" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <?php if(in_array('main', $class)): ?>
                  <span class="fa fa-angle-left hidden-xs"></span> &nbsp;
                <?php endif ?>
                <?= $item['text'] ?>
                <?php if(in_array('main', $class)): ?>
                  &nbsp; <span class="fa fa-angle-right visible-xs"></span>
                <?php endif ?>
                <?php if(in_array('sidebar', $class)): ?>
                  &nbsp; <span class="fa fa-angle-down"></span><span class="fa fa-angle-up"></span>
                <?php endif ?>
            </a>
            <ul class="nav submenu">
                <?php if($class == 'main'): ?>
                    <li class="visible-xs"><a href="#" class="back"><i class="fa fa-angle-left"></i> <?= $this->text('regular-cancel') ?></a></li>
                <?php endif ?>
                <?php foreach($submenu as $l => $i): ?>
                    <?= $this->insert('partials/header/menu_item', ['link' => $l, 'item' => $i]) ?>
                <?php endforeach ?>
            </ul>
        <?php else: ?>
            <a href="<?= $item['link'] ?>"<?= $item['a_class'] ? ' class="' . $item['a_class'] .'"' : '' ?>><?= $item['text'] ?></a>
        <?php endif ?>
    </li>
<?php else: ?>
    <li><a href="<?= $link ?>"><?= $item ?></a></li>
<?php endif ?>
