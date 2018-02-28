<?php

$value = is_array($this->value) ? $this->value : [$this->value];

?>
<span class="roles">
<?php
  foreach($value as $role => $label) {
    if(count($value) > 1 && $role === 'user') continue;
    $class = 'default';
    if(in_array($role, ['admin', 'superadmin'])) $class = 'danger';
    elseif('consultant' === $role) $class = 'warning';
    elseif('manager' === $role) $class = 'primary';
    elseif('translator' === $role) $class = 'success';
    elseif('user' !== $role) $class = 'info';

    echo "<span class=\"label label-$class\">$label</span> ";
  }
?>
</span>
