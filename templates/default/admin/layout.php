<?php
/*
    Base layout for admin
 */

use Goteo\Core\View,
    Goteo\Controller\AdminController as Admin;

// piñones usuarios
$allowed = Admin::$supervisors[$_SESSION['user']->id];

if (isset($allowed) && !empty($vars['folder']) && !in_array($vars['folder'], $allowed)) {
    header('Location: /admin/');
    exit;
}

$this->layout('layout', [
    'bodyClass' => 'admin',
    'jsreq_autocomplete' => true,
    ]);
?>

<?php $this->section('sub-header') ?>
    <?= $this->insert('admin/partials/breadcrumb') ?>
<?php $this->replace() ?>

<?php $this->section('content') ?>

        <div id="main">

            <div class="admin-center">

            <?= $this->supply('admin-menu', $this->insert('admin/partials/menu')) ?>

            <?php echo $this->supply('admin-content') ?>

            <?php echo $this->supply('admin-aside') ?>

            </div> <!-- fin center -->

        </div> <!-- fin main -->

<?php $this->replace() ?>


<?php $this->section('footer') ?>
<script type="text/javascript">
$(function(){
    $('#select-node').change(function(e){
        e.preventDefault();
        location.search = '?node=' + $(this).val();
    });
});
</script>
<?php $this->append() ?>
