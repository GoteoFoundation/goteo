<div class="admin-menu">

    <?php foreach ($this->admin_menu as $sCode => $section) : ?>
    <fieldset>
        <legend><?php echo $section['label'] ?></legend>
        <ul class="ul-admin">
        <?php foreach ($section['options'] as $oCode => $option) :
            echo '<li' . ( $oCode === $this->option ? ' class="selected"' : '') . '><a href="/admin/'.$oCode.'">'.$option['label'].'</a></li>';
        endforeach; ?>
        </ul>
    </fieldset>
    <?php endforeach; ?>
</div>

<?php if($this->get_user()->roles['superadmin']): ?>

    <div class="widget board">
        <ul class="ul-admin">
            <li<?= ('projects' === $this->option ? ' class="selected"' : '') ?>><a href="/admin/projects">Proyectos</a></li>
            <li<?= ('users' === $this->option ? ' class="selected"' : '') ?>><a href="/admin/users">Usuarios</a></li>
            <li<?= ('accounts' === $this->option ? ' class="selected"' : '') ?>><a href="/admin/accounts">Aportes</a></li>
            <li<?= ('calls' === $this->option ? ' class="selected"' : '') ?>><a href="/admin/calls">Convocatorias</a></li>
            <li<?= ('tasks' === $this->option ? ' class="selected"' : '') ?>><a href="/admin/tasks">Tareas</a></li>
            <li<?= ('nodes' === $this->option ? ' class="selected"' : '') ?>><a href="/admin/nodes">Nodos</a></li>
            <li<?= ('reports' === $this->option ? ' class="selected"' : '') ?>><a href="/admin/reports">Informes</a></li>
            <li<?= ('newsletter' === $this->option ? ' class="selected"' : '') ?>><a href="/admin/newsletter">Boletin</a></li>
        </ul>
    </div>

<?php endif; ?>
