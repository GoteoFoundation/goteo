<div class="btn-group<?= $this->class ? ' ' . $this->class : '' ?>">
  <button type="button" class="btn btn-cyan dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-language"></i> <?php
        if($this->title) echo $this->title;
        elseif($this->current) echo $this->a('languages')[$this->current];
        else echo $this->text('regular-translations');
     ?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <?php
    foreach($this->a('languages') as $key => $lang):
        $class = '';
        if(in_array($key, $this->a('skip'))) continue;
        if(in_array($key, $this->a('translated'))) $class = 'available';
        if($this->current == $key) $class .= ' active';
    ?>
        <li<?= $class ? ' class="' . $class . '"' : '' ?>><a href="<?= $this->base_link . $key ?>"><?= $lang ?></a></li>
    <?php endforeach ?>
    <?php if($this->exit_link): ?>
        <li role="separator" class="divider"></li>
        <li><a href="<?= $this->exit_link ?>"><?= $this->exit ? $this->exit : $this->text('regular-cancel') ?></a></li>
    <?php endif ?>
  </ul>
</div>
