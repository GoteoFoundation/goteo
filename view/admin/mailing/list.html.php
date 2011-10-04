<?php

use Goteo\Library\Text;

?>
<div class="widget board">
    <form id="filter-form" action="/admin/mailing/edit" method="post">
        <label for="status-filter">Mostrar por estado:</label>
        <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
            <option value="">Todos los estados</option>
        <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
            <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
        <?php endforeach; ?>
        </select>

        <label for="interest-filter">Mostrar usuarios interesados en:</label>
        <select id="interest-filter" name="interest" onchange="document.getElementById('filter-form').submit();">
            <option value="">Cualquier interés</option>
        <?php foreach ($this['interests'] as $interestId=>$interestName) : ?>
            <option value="<?php echo $interestId; ?>"<?php if ($filters['interest'] == $interestId) echo ' selected="selected"';?>><?php echo $interestName; ?></option>
        <?php endforeach; ?>
        </select>


        <label for="role-filter">Mostrar usuarios con rol:</label>
        <select id="role-filter" name="role" onchange="document.getElementById('filter-form').submit();">
            <option value="">Cualquier rol</option>
        <?php foreach ($this['roles'] as $roleId=>$roleName) : ?>
            <option value="<?php echo $roleId; ?>"<?php if ($filters['role'] == $roleId) echo ' selected="selected"';?>><?php echo $roleName; ?></option>
        <?php endforeach; ?>
        </select>

        <br />
        <!--
        <label for="name-filter">Por nombre o email:</label>
        <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" />
        -->

        <input type="submit" name="select" value="Nueva comunicación">

    </form>
</div>