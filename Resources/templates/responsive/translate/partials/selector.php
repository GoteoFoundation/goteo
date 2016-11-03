<?php

$zones = [];
foreach($this->zones as $k => $v) {
    $tk = $this->text("translator-$k");
    if(is_array($v)) {
        $zones[$tk] = [];
        foreach($v as $sk) {
            $zones[$tk][$sk] = $this->text("translator-$sk");
        }
    }
    else {
        $zones[$tk] = $v;
    }
}

$query = $this->get_query() ? '?'. http_build_query($this->get_query()) : '';

?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/translate"><?= $this->text('translator') ?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbar-collapse-1">


    <?php if($this->zone && !$this->id): ?>
      <form class="navbar-form navbar-left" action="/translate/<?= $this->zone . $query ?>" method="get">
        <div class="form-group">
          <input type="text" name="q" class="form-control" placeholder="Search" value="<?= $this->get_query('q') ?>">
        </div>
        <div class="form-group">
        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> <?= $this->text('regular-search') ?></button>

        <?= $this->text('translator-only-pendings') ?>
        <?= $this->html('select', ['options' => ['' => '---'] + $this->languages, 'value' => $this->get_query('p'), 'name' => 'p', 'attribs' => ['id' => 'select-pending', 'class' => 'form-control']]) ?>
        </div>
      </form>
    <?php endif ?>

      <form class="navbar-form navbar-right">
          <?php if($this->id): ?>
            <a class="btn btn-primary" href="/translate/<?= $this->zone . $query ?>">
                <span class="glyphicon glyphicon-list"></span>
                <?= $this->text('translator-back-to-list') ?>
            </a>
          <?php endif ?>
        <div class="form-group">
        <?= $this->html('select', ['options' => ['' => '↓ ' . $this->text('translator-choose') . ' ↓'] + $zones, 'value' => $this->zone, 'name' => 'select-zone', 'attribs' => ['id' => 'select-zone', 'class' => 'form-control']]) ?>
        </div>
      </form>


    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
