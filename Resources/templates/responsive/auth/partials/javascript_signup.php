<script type='text/javascript'>

$(function() {

  $('#register_accept').change(function() {
        $('#register_continue').attr('disabled', !this.checked);

  });

  $('#openid').change(function() {
        $('#openid-link').attr('href', '/login/openid?return=<?= urlencode($this->raw('return')) ?>&u='+$(this).val());

  });

})

</script>
