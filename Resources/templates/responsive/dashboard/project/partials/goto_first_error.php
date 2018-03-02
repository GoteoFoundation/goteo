<?php
if(!$this->validation) return;
if($this->validation->{$this->zone} == 100) return;
if(!$this->has_query('validate')) return;

$show = false;
foreach($this->validation->errors[$this->zone] as $err) {
    if($err == $this->zone) {
        $show = true;
    }
}

if(!$show) return;
?>

<p><button class="goto-first-error btn btn-danger btn-xs"><i class="fa fa-hand-o-right"></i> <?= $this->text('dashboard-project-goto-first-error') ?></button></p>
