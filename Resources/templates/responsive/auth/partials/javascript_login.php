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
