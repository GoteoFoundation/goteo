<?php

$this->layout('auth/layout', [
    'alt_title' => $this->text('login-title'),
    'alt_description' => $this->text('login-title')
    ]);

$this->section('inner-content');

?>
    <h2 class="col-md-offset-1 padding-bottom-6"><?= $this->text('login-title') ?></h2>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

    <form class="form-horizontal" role="form" method="POST" action="/login?return=<?= urlencode($this->raw('return')) ?>&amp;lang=<?= $this->lang_current() ?>">

    <?= $this->insert('auth/partials/form_login') ?>

    <?= $this->insert('auth/partials/social_login') ?>

    </form>

<?php $this->replace() ?>

<?php $this->section('content') ?>
    <?= $this->insert('auth/partials/recover_modal') ?>

    <?= $this->insert('auth/partials/openid_modal') ?>
<?php $this->append() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    var _get_ajax_password_result = function() {
        var email=$("#password-recover-email").val();

        $.ajax({
            url: "/password-recovery",
            data: { 'email' : email, 'return' : '<?= urlencode($this->raw('return')) ?>'  },
            type: 'post',
            success: function(result){
                $("#modal-content").html(result);
            }
        });
   };

   $("#myModal").on('keypress', "#password-recover-email", function (e) {
        if (e.keyCode == 10 || e.keyCode == 13) {
            e.preventDefault();
            _get_ajax_password_result();
            return false;
        }
    });

    $('#myModal').on('shown.bs.modal', function () {
        $('#myModal input:first').focus();
    });

    $("#myModal").on('click', '#btn-password-recover', function(){
       _get_ajax_password_result();
    });

    $('#openid').change(function() {
        $('#openid-link').attr('href', '/login/openid?return=<?= urlencode($this->raw('return')) ?>&u='+$(this).val());

    });

});

// @license-end
</script>

<?php $this->append() ?>
