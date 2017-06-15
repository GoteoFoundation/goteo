<div class="sidebar-header">
    <a class="navbar-brand" href="<?= SITE_URL ?>"><img src="<?= SRC_URL ?>/goteo_logo.png" class="img-responsive logo" alt="Goteo"></a>
    <button class="toggle-menu btn btn-link" data-target="<?= $this->target ?>" title="Close"><i class="fa fa-close"></i></button>
</div>

<ul class="nav">
  <?php foreach($this->raw('menu') as $link => $text): ?>
    <?php if(is_array($text)): ?>
        <li><a href="<?= $text['link'] ?>" class="<?= $text['class'] ?>"><?= $text['text'] ?></a></li>
    <?php else: ?>
        <li><a href="<?= $link ?>"><?= $text ?></a></li>
    <?php endif ?>
  <?php endforeach ?>
</ul>

<?php if($this->bottom): ?>
<ul class="nav bottom">
  <?php foreach($this->raw('bottom') as $link => $text): ?>
    <?php if(is_array($text)): ?>
        <li><a href="<?= $text['link'] ?>" class="<?= $text['class'] ?>"><?= $text['text'] ?></a></li>
    <?php else: ?>
        <li><a href="<?= $link ?>"><?= $text ?></a></li>
    <?php endif ?>
  <?php endforeach ?>
</ul>
<?php endif ?>
