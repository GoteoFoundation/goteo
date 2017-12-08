<?php
    $languages = $this->a('languages');
    if($this->default_title) $title = $this->default_title;
    elseif($this->lang) $title = $this->a('languages')[$this->lang];
    else $title = $this->text('regular-translations');

?><div class="btn-group<?= $this->class ? ' ' . $this->class : '' ?>">
  <button type="button" class="btn <?= $this->btn_class ? $this->btn_class : 'btn-cyan' ?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="<?= $title ?>">
    <i class="fa fa-globe"></i> <?= $this->no_title ? '' :" $title " ?> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu dropdown-menu-right">
    <?php
    $empty = true;
    foreach($languages as $key => $lang):
        $class = '';
        $badge = '';
        if(in_array($key, $this->a('skip'))) continue;
        if(in_array($key, $this->a('translated'))) {
            $class = 'available';
            if($this->percentModel) {
                $badge = ' ' . $this->percent_badge($this->percentModel->getLangsPercent($key));
            }
        }
        if($this->lang == $key) $class .= ' active';
        $empty = false;
    ?>
        <li<?= $class ? ' class="' . $class . '"' : '' ?>><a href="<?= $this->base_link . $key ?>"><?= $lang ?><?= $badge ?></a></li>
    <?php endforeach ?>
    <?php

    if($empty) {
        if($this->project) {
            $link = '/dashboard/project/' . $this->project->id . '/translate';
        }
        if($link) {
            echo '<li class="no-bind"><a href="'. $link . '">' . $this->text('dashboard-translate-project-empty') .'</a></li>';
        } else  {
            echo '<li class="no-bind"><a>' . $this->text('dashboard-translate-empty') .'</a></li>';
        }
    }
    ?>
    <?php if($this->exit_link): ?>
        <li role="separator" class="divider"></li>
        <li><a href="<?= $this->exit_link ?>"><?= $this->exit ? $this->exit : $this->text('regular-cancel') ?></a></li>
    <?php endif ?>
  </ul>
</div>
