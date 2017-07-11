<script type='text/javascript'>

$(function() {

  $('#register_accept').change(function() {
    $(this).closest('.form-group').removeClass('has-error');
    $(this).closest('.form-group').find('.info-block').remove();
    $('#register_continue').attr('disabled', !this.checked);

  });

  $('#openid').change(function() {
    $('#openid-link').attr('href', '/login/openid?return=<?= urlencode($this->raw('return')) ?>&u='+$(this).val());

  });

  // UserId suggestions
  $('input[name="email"],input[name="name"],input[name="userid"]').on('change', function(e) {
    var $email = $('input[name="email"]');
    var $name = $('input[name="name"]');
    var $userid = $('input[name="userid"]');
    var $ugroup = $userid.closest('.form-group');
    var $self = $(this);
    var $group = $self.closest('.form-group');
    var key = $(this).attr('name');
    var val = $(this).val();
    var vars = {
      seed: [$email.val(), $name.val(), $userid.val()]
    };

    vars[key] = val.trim();
    if(!vars[key]) {
      return;
    }
    if(key === 'userid') {
      $self.addClass('user-edited');
    }
    $.getJSON('/api/login/check', vars, function(result) {
      $group.find('.info-block').remove();
      if(result.available) {
        $group.removeClass('has-error').addClass('has-success');
      } else {
        $group.removeClass('has-success').addClass('has-error');
        $('<span class="help-block info-block"><?= $this->ee($this->text('oauth-goteo-user-password-exists'), 'js') ?></span>').insertAfter($self);
      }
      if(!$userid.hasClass('user-edited')) {
        $userid.val(result.suggest.pop());
        $ugroup.removeClass('has-error').addClass('has-success');
      }
      $ugroup.find('.suggest-block').remove();
      if(result.suggest.length) {
        $('<span class="help-block suggest-block"><?= $this->ee($this->text('login-alternate-ids'), 'js') ?>: ' + result.suggest.map(function(v) {
        return '<a href="#" class="userid-suggestion">' + v + '</a>';
        }).join(', ') + '</span>').insertAfter($userid);
      }
      });
  });

  $('.form-group').on('click', '.userid-suggestion', function(e) {
    e.preventDefault();
    var $userid = $('input[name="userid"]');
    $userid.addClass('user-edited').val($(this).text());
    $userid.change();
  });

  $('input[name="password"],input[name="rpassword"]').on('change', function(e) {
    $p1 = $('input[name="password"]');
    $p2 = $('input[name="rpassword"]');
    var $group1 = $p1.closest('.form-group');
    var $group2 = $p2.closest('.form-group');

    $group2.find('.info-block').remove();
    if($p1.val() === $p2.val()) {
      $group1.removeClass('has-error').addClass('has-success');
      $group2.removeClass('has-error').addClass('has-success');
    } else {
      $group1.removeClass('has-success').addClass('has-error');
      $group2.removeClass('has-success').addClass('has-error');
      $('<span class="help-block info-block"><?= $this->ee($this->text('error-register-password-confirm'), 'js') ?></span>').insertAfter($p2);
    }
  });
})

</script>
