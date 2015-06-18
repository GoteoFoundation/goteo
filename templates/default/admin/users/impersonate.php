<?php $this->layout('admin/users/view_layout') ?>

<?php $this->section('admin-user-board') ?>

<?php

$user = $this->user;

?>

    <form action="<?= '/' . $this->template . '/' . $user->id ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $user->id ?>" />

        <input type="submit" class="red" name="impersonate" value="Suplantar a este usuario" onclick="return confirm('Estás completamente seguro de entender lo que esás haciendo?');" /><br />
        <span style="font-style:italic;font-weight:bold;color:red;">Atención!! Con esto vas a dejar de estar logueado como el superadmin que eres y pasarás a estar logueado como este usuario con todos sus permisos y restricciones.</span>

    </form>

<?php $this->append() ?>
