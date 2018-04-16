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
    $('.auto-hide .more').on('click', function(e) {
        e.preventDefault();
        $(this).closest('.auto-hide').toggleClass('show');
    });

    $(".auto-update-projects").on('change', ".interest", function (e) {
        var value = $(this).is(":checked") ? 1 : 0;
        var id = $(this).attr('id');
        var $parent = $(this).closest('.auto-update-projects');
        var $button = $parent.find('.more-projects-button');
        var url = $parent.data('url');
        var limit = $parent.data('limit') || 6;

        $.post(url + '?' + $.param({ limit: limit }), { 'id' : id, 'value' : value  }, function(result) {
            if((result.offset + result.limit) >= result.total) {
                $button.addClass('hidden');
            } else {
                $button.removeClass('hidden');
            }
            $parent.contents('.elements-container').html(result.html);
        });
    });

    $(".auto-update-projects").on('click', ".more-projects-button", function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.auto-update-projects');
        var $button = $parent.find('.more-projects-button');
        var total_elements = $parent.find('.widget-element').length;
        var url = $parent.data('url');
        var total = $parent.data('total');
        var limit = $parent.data('limit') || 6;

        $.get(url, {offset:total_elements, limit: limit}, function(result) {
            if((result.offset + result.limit) >= result.total) {
                $button.addClass('hidden');
            } else {
                $button.removeClass('hidden');
            }
            $parent.contents('.elements-container').append(result.html);
        });
    });


    // Send comments
    $(".ajax-comments").on('click', ".send-comment", function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.ajax-comments');
        var $list = $($parent.data('list'));
        var url = $parent.data('url');
        var $error = $parent.find('.error-message');
        var $textarea = $parent.find('[name="message"]');
        var $recipients = $('.ajax-comments .recipients');
        var recipients = [];
        $recipients.find('.label').each(function(){
            recipients.push($(this).data('user'));
        });
        var data = {
            message: $textarea.val(),
            recipients: recipients,
            thread: $parent.data('thread'),
            project: $parent.data('project'),
            admin: $parent.data('admin'),
            view: 'dashboard'
        }
        $error.addClass('hidden').html('');
        $.post(url, data, function(data) {
            // console.log('ok!', data);
            $list.append(data.html);
            $textarea.val('');
            $recipients.find('.text').html($recipients.data('public'));
          }).fail(function(xhr) {
            // console.log('error', xhr);
            var error;
            try {
                error = JSON.parse(xhr.responseText).error;
            } catch(e) {
                error = xhr.statusText;
            }
            $error.removeClass('hidden').html(error);
          });
    });

    // Delete comments
    $('.comments-list').on('click', ".delete-comment", function (e) {
        e.preventDefault();
        var ask = $(this).data('confirm');
        var url = $(this).data('url');
        var $item = $(this).closest('.comment-item');
        var $error = $item.find('.error-message');
        if(confirm(ask)) {
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function(data) {
                  // console.log('success', data);
                  $item.remove();
                }
            }).fail(function(data) {
              var error = JSON.parse(data.responseText);
              // console.log('error', data, error)
              $error.removeClass('hidden').html(error.error);
            });
        }
    });

    var addRecipient = function($recipients, user, name) {
        $recipients.find('.text').html($recipients.data('private'));
        if(!$recipients.find('[data-user="' + user +'"]').length) {
            $recipients.append(' <span class="label label-lilac" data-user="' + user + '">' + name + ' <i class="fa fa-close"></i></span>');
        }
    };
    // add to private list
    $('.comments-list').on('click', '.send-private', function (e) {
        e.preventDefault();
        var user = $(this).data('user');
        var name = $(this).data('name');
        var $recipients = $(this).closest('.comments').find('.recipients');
        addRecipient($recipients, user, name);
    });

    // remove from private list
    $(".ajax-comments .recipients").on('click', ".label>i", function (e) {
        // var $recipients = $(".ajax-comments .recipients");
        var $recipients = $(this).closest('.recipients');
        var $checkbox = $('.ajax-comments input[name="private"]');
        $(this).parent().remove();
        if(!$recipients.find('.label').length) {
            // console.log('recipients',$recipients.find('.label'), $recipients.html());
            $recipients.find('.text').html($recipients.data('public'));
            $checkbox.prop('checked', false);
        }
    });

    $(".ajax-comments").on('click', 'input[name="private"]', function (e) {
        e.preventDefault();
        var $recipients = $(this).closest('.ajax-comments').find('.recipients');
        var $list = $(this).closest(".comments").find('.comments-list');

        $(this).prop('checked', true);
        $list.find('.send-private').each(function() {
            addRecipient($recipients, $(this).data('user'), $(this).data('name'));
        });
    });


    // messages handle
    $(".ajax-message").on('click', ".send-message", function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.ajax-message');
        // var $list = $($parent.data('list'));
        var url = $parent.data('url');
        var $error = $parent.find('.error-message');
        var $subject = $parent.find('[name="subject"]');
        var $body = $parent.find('[name="body"]');
        var $reward = $parent.find('[name="reward"]');
        var $filter = $parent.find('[name="filter"]');
        var $users = $parent.find('[name="users"]');

        var data = {
            subject: $subject.val(),
            body: $body.val(),
            thread: $parent.data('thread'),
            reward: $reward.val(),
            filter: $filter.val(),
            users: $users.val().split(','),
            project: $parent.data('project'),
            // view: 'dashboard'
        }
        $error.addClass('hidden').html('');
        $.post(url, data, function(response) {
            // console.log('ok!', response);
            // $list.append(response.html);
            $(document).trigger('message-sent', [data, response]);
          }).fail(function(xhr) {
            // console.log('error', xhr);
            var error;
            try {
                error = JSON.parse(xhr.responseText).error;
            } catch(e) {
                error = xhr.statusText;
            }
            $error.removeClass('hidden').html(error);
          });
    });
});
