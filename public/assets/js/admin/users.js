/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/

$(function(){

    var checkUserLogin = function(field, callback) {
        var $email = $('input#autoform_email');
        var $userid = $('input#autoform_userid');
        var $self = $('input#autoform_' + field);

        var vars = { seed: [$email.val(), $userid.val()] };
        vars[field] = $self.val();

        $self.addClass('loading');
        $.getJSON('/api/login/check', vars, function(result) {
            console.log(result);
            $self.removeClass('loading');
            if(result) {
                if(result.available) {
                    $self.closest('.form-group').removeClass('has-error').addClass('has-success');
                } else {
                    $self.closest('.form-group').removeClass('has-success').addClass('has-error');
                }
                if(typeof callback === 'function') {
                    callback(result);
                }
            }
        });
    };

    var generatePassword = function() {
        var length = 8,
            charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
            retVal = "";
        for (var i = 0, n = charset.length; i < length; ++i) {
            retVal += charset.charAt(Math.floor(Math.random() * n));
        }
        return retVal;
    };

    $('#main').on('change', 'input#autoform_email,input#autoform_name,input#autoform_userid', function(e) {
        var id = $(this).attr('id').substr(9);
        var $userid = $('input#autoform_userid');
        var $name = $('input#autoform_name');
        var $password = $('input#autoform_password');
        var parts, suggest;

        checkUserLogin(id);

        if(id === 'email') {
            parts = $(this).val().split('@');
            suggest = parts[0].charAt(0).toUpperCase() + parts[0].substr(1);
            if($name.val().trim() == '') {
                $name.val(suggest);
            }
            if($userid.val().trim() == '') {
                checkUserLogin('userid', function(result) {
                    $userid.val(result.suggest[0]);
                    $userid.closest('.form-group').removeClass('has-error').addClass('has-success');
                });
            }
        }

        if($password.val().trim() == '') {
            $password.val(generatePassword());
        }
    });


});
