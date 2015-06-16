<div class="admin-menu">
    <?php foreach ($this->admin_menu as $sCode => $section) : ?>
    <fieldset>
        <legend><?php echo $section['label'] ?></legend>
        <ul>
        <?php foreach ($section['options'] as $oCode=>$option) :
            echo '<li><a href="/admin/'.$oCode.'">'.$option['label'].'</a></li>';
        endforeach; ?>
        </ul>
    </fieldset>
    <?php endforeach; ?>
</div>

<?php if($this->get_user()->roles['superadmin']): ?>

   <div class="widget board">
        <ul>
            <li><a href="/admin/projects">Proyectos</a></li>
            <li><a href="/admin/users">Usuarios</a></li>
            <li><a href="/admin/accounts">Aportes</a></li>
            <li><a href="/admin/calls">Convocatorias</a></li>
            <li><a href="/admin/tasks">Tareas</a></li>
            <li><a href="/admin/nodes">Nodos</a></li>
            <li><a href="/admin/reports">Informes</a></li>
            <li><a href="/admin/newsletter">Boletin</a></li>
        </ul>
    </div>

<?php endif; ?>
