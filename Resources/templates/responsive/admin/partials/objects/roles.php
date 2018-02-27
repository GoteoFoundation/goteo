<?php

$value = is_array($this->value) ? $this->value : [$this->value];

?>
<span class="roles">
<?php
  foreach($value as $v) {
    if(count($value) > 1 && $v === 'user') continue;
    $class = 'default';
    if(in_array($v, ['admin', 'superadmin'])) $class = 'danger';
    elseif('consultant' === $v) $class = 'warning';
    elseif('manager' === $v) $class = 'primary';
    elseif('translator' === $v) $class = 'success';
    elseif('user' !== $v) $class = 'info';

    echo "<span class=\"label label-$class\">$v</span> ";
  }
?>
</span>
