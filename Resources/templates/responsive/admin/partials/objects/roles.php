<?php

if(is_array($this->value)) $value = implode(', ', $this->value);

$class = 'roles label';
if($this->last) $class .= ' last';

if(in_array('admin', $this->value)) $class .= ' label-danger';
elseif(in_array('consultant', $this->value)) $class .= ' label-warning';
elseif(in_array('manager', $this->value)) $class .= ' label-primary';
elseif(in_array('translator', $this->value)) $class .= ' label-success';
elseif(count($this->value) > 1) $class .= ' label-info';
elseif(in_array('user', $this->value)) $class .= ' label-default';

?>
<span class="<?= $class ?>"><?= $value ?></span>
