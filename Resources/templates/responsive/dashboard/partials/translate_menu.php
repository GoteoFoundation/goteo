<div class="btn-group<?= $this->class ? ' ' . $this->class : '' ?>">
  <button type="button" class="btn btn-cyan dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-language"></i> <?= $this->text('regular-translate') ?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <?php foreach($this->a('languages') as $key => $lang): ?>
        <li><a href="<?= $this->base_link . $key ?>"><?= $lang ?></a></li>
    <?php endforeach ?>
  </ul>
</div>
