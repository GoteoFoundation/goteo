<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-rewards') ?></h2>


    <?php if($this->project->inCampaign()): ?>
        <p><?= $this->text('dashboard-rewards-notice') ?></p>
    <?php endif ?>

    <form id="filters">
      <div class="row">
        <div class="col-xs-10">
            <p><?= $this->html('input',
                        ['type' => 'text',
                        'name' => 'filter[query]',
                        'value' => $this->filter['query'],
                        'attribs' => [
                            'id' => 'filter-query',
                            'class' => 'form-control',
                            'placeholder' => $this->text('regular-search-user')
                        ],
                        'options' => $this->filters['query']
                    ]) ?></p>

        </div>
        <div class="col-xs-2">
            <button type="submit" class="btn btn-cyan" ><i class="fa fa-search"></i> <?= $this->text('regular-search') ?></button>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6">
            <label for="filter-reward"><?= $this->text('dashboard-project-filter-by-reward') ?></label>
            <?= $this->html('input',
                        ['type' => 'select',
                        'name' => 'filter[reward]',
                        'value' => $this->filter['reward'],
                        'attribs' => [
                            'id' => 'filter-reward',
                            'class' => 'form-control'
                        ],
                        'options' => $this->filters['reward']
                    ]) ?>
        </div>
        <div class="col-xs-6">
            <label for="filter-others"><?= $this->text('dashboard-project-filter-by-others') ?></label>
            <?= $this->html('input',
                        ['type' => 'select',
                        'name' => 'filter[others]',
                        'value' => $this->filter['others'],
                        'attribs' => [
                            'id' => 'filter-others',
                            'class' => 'form-control'
                        ],
                        'options' => $this->filters['others']
                    ]) ?>
        </div>
      </div>
      <input type="hidden" name="order" value="<?= $this->order ?>">
    </form>

    <h5><?= $this->text('dashboard-search-invests-totals', ['%TOTAL_INVESTS%' => '<strong>' . $this->total_invests . '</strong>', '%TOTAL_USERS%' => '<strong>' . $this->total_users . '</strong>', '%TOTAL_AMOUNT%' => '<strong>' . amount_format($this->total_amount) . '</strong>']) ?></h5>

    <div class="row spacer-bottom-20">
        <div class="col-lg-6">
        <a data-toggle="modal" href="#messageModal" class="btn btn-cyan"><i class="fa fa-paper-plane-o"></i> <?= $this->text('dashboard-search-invests-msg') ?></a>
        </div>
        <div class="col-lg-6 exportcsv" style="padding-top: 5px">
            <?= $this->text('dashboard-rewards-investors_table', ['%URL%' => '/api/projects/' . $this->project->id . '/invests/csv']) ?>
        </div>
    </div>

    <table class="-footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs"><?= $this->insert('dashboard/partials/table_th', ['text' => '#', 'field' => 'id']) ?></th>
          <th data-type="date" data-breakpoints="xs"><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('regular-date'), 'field' => 'invested']) ?></th>
          <th data-type="html" data-breakpoints="xs"><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('admin-user'), 'field' => 'user']) ?></th>
          <th><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('invest-amount'), 'field' => 'amount']) ?></th>
          <th><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('rewards-field-individual_reward-reward'), 'field' => 'reward']) ?></th>
          <th><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('dashboard-rewards-fulfilled_status'), 'field' => 'fulfilled']) ?></th>
          <th><?= $this->text('admin-address') ?></th>
          <th><?= $this->text('regular-actions') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php if($this->invests): ?>

      <?php

        foreach($this->invests as $invest):
            $resign = $invest->resign;
            $uid = $invest->getUser()->id;
            $name = $invest->getUser()->name;
            $email = $invest->getUser()->email;
            $a = $invest->getAddress();
            $address = $a->address . ', ' . $a->location . ', ' . $a->zipcode .' ' . $a->country;
            $reward = $invest->getRewards() ? $invest->getRewards()[0]->getTitle() : '';
            if($invest->resign) {
                $reward = $address = '';
                if($invest->anonymous) {
                    $uid = $name = $email = '';
                }
                $reward = '<span class="label label-info">'.$this->text('dashboard-rewards-resigns').'</span>';
            }
            if($invest->campaign) {
                $email = $address = $reward = '';
                $resign = true;
                $reward = '<span class="label label-lilac">'.$this->text('regular-matchfunding').'</span>';
            }
            if(!$resign && !$reward) {
                $reward = '<span class="label label-danger">' . $this->text('regular-unknown') . '</span>';
            }


      ?>
        <tr<?= $invest->isCharged() ? '' : ' class="strikethrough"'?>>
          <td><?= $invest->id ?></td>
          <td><?= date_formater($invest->invested) ?></td>
          <td><?php if($uid): ?><img src="<?= $invest->getUser()->avatar->getLink(30, 30, true) ?>" alt="<?= $name ?>" class="img-circle"> <?= $name ?><?php else: ?><?= $this->text('regular-anonymous') ?><?php endif ?> </td>
          <td><?= amount_format($invest->amount) ?></td>
          <td><?= $reward ?></td>
          <td>
              <?php if(!$invest->isCharged()): ?>
                <span class="label label-danger"><?= $invest->getStatusText(true) ?></span>
              <?php elseif($invest->resign): ?>
                &nbsp;
              <?php elseif($invest->fulfilled): ?>
                <span class="label label-cyan"><?= $this->text('regular-yes') ?></span>
              <?php else: ?>
              <?= $this->insert('dashboard/project/partials/boolean', ['active' => $invest->fulfilled, 'name' => 'fulfilled-' . $invest->id, 'label_type' => 'cyan', 'url' => '/api/projects/' . $this->project->id . '/invests/' . $invest->id . '/fulfilled', 'confirm_yes' => $this->text('dashboard-rewards-process_alert') ]) ?>
              <?php endif ?>
          </td>
          <td><?= $address ?></td>
          <td>
            <a data-toggle="modal" href="#messageModal" data-user="<?= $invest->getUser()->id ?>" data-name="<?= $invest->getUser()->name ?>" class="send-private" title="<?= $this->text('support-send-private-message') ?>"><span><?= (int)$this->messages[$invest->getUser()->id] ?></span> <i class="icon-1x icon icon-partners"></i></a>
          </td>
        </tr>
      <?php endforeach ?>
    <?php else: ?>
        <tr><td colspan="8"><h4><?= $this->text('dashboard-project-no-invests') ?></h4></td></tr>
    <?php endif ?>
      </tbody>
    </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total_invests, 'limit' => $this->limit]) ?>

  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="messageModalLabel"><?= $this->text('dashboard-new-message-to-donors') ?></h4>
      </div>
      <div class="modal-body">
        <div class="messages-list"></div>
        <?= $this->insert('dashboard/project/partials/message', [
            'reward' => $this->filter['reward'],
            'filter' => $this->filter['others'],
            'project' => $this->project->id
            ]) ?>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script class="item_message_template" type="text/template">

  <div class="media comment-item">
    <div class="media-left">
        <img title="{name}" src="{avatar}" class="img-circle">
    </div>
    <div class="media-body">
        <h4> <?= $this->text('mailer-from') ?>:
            <strong>{name}</strong>
            - <em>{date}</em>
            <span class="recipient">
                <?= $this->text('mailer-to') ?>:
                <strong>{recipient}</strong>
            </span>
        </h4>
        <p>{message}</p>
        <p class="text-danger hidden error-message"></p>
    </div>
  </div>

</script>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    $('.exportcsv a').on('click', function(){
        alert('<?= $this->ee($this->text('dashboard-investors_table-disclaimer'), 'js') ?>');
    });
    $('#filters select').on('change', function(){
        $(this).closest('form').submit();
    });
    $(document).on('form-boolean-changed', function(evt, input){
        // console.log('changed', input, $(input), $(input).closest('div'));
        if($(input).prop('checked')) {
            $(input).closest('.material-switch').replaceWith('<span class="label label-cyan"><?= $this->text('regular-yes') ?></span>');
        }
    });

    var texts = {
        all: '<?= $this->ee($this->text('dashboard-rewards-massive_msg-all'), 'js') ?>',
        donors: '<?= $this->ee($this->text('dashboard-message-donors-donors'), 'js') ?>',
        and: '<?= $this->ee($this->text('dashboard-message-donors-and'), 'js') ?>',
        reward: '<?= $this->ee($this->text('dashboard-message-donors-reward'), 'js') ?>',
        donative: '<?= $this->ee($this->text('dashboard-message-donors-donative'), 'js') ?>',
        nondonative: '<?= $this->ee($this->text('dashboard-message-donors-nondonative'), 'js') ?>',
        pending: '<?= $this->ee($this->text('dashboard-message-donors-pending'), 'js') ?>',
        fulfilled: '<?= $this->ee($this->text('dashboard-message-donors-fulfilled'), 'js') ?>',
        error: '<?= $this->ee($this->text('dashboard-message-donors-error'), 'js') ?>',
        total: '<?= $this->ee($this->text('dashboard-message-donors-total'), 'js') ?>'
    };
    var total_users = <?= (int)$this->total_users ?>;

    // Message management
    $('#messageModal').on('shown.bs.modal', function (evt) {
        // console.log('modal evt', $(evt.relatedTarget).attr('class'), evt);
        var txt = '';
        var private = $(evt.relatedTarget).hasClass('send-private');
        var user_id = $(evt.relatedTarget).data('user');
        var user_txt = $(evt.relatedTarget).data('name');
        var $span = $(evt.relatedTarget).find('>span');
        var $subject = $('#messageModal input[name="subject"]').closest('.form-group');
        var $list = $('#messageModal .messages-list');
        var $recipients = $('#messageModal .ajax-message .recipients');
        var prefix = $recipients.data('private');
        var reward_id = $('#filter-reward').val();
        var reward_txt = $('#filter-reward option:selected').text();
        var others_id = $('#filter-others').val();
        var others_txt = $('#filter-others option:selected').text();

        // Create form fields
        $('#messageModal .ajax-message .error-message').addClass('hidden');
        $list.removeClass('loading').html('');
        $subject.show();
        if(private) {
          if(parseInt($span.text()) > 0) {
            $subject.hide();
          }
          // Create messages
          var $template = $('script.item_message_template');
          $list.addClass('loading');
          $.getJSON('/api/projects/<?= $this->project->id ?>/messages/' + user_id, function(msgs) {
            // console.log('msgs', msgs);
            if(msgs && msgs.list) {
              $list.removeClass('loading');
              $.each(msgs.list, function(i, item){
                // console.log(i, item);
                var msg = $template.html()
                            .replace(/\{name\}/g, item.name)
                            .replace(/\{recipient\}/g, item.recipient_name)
                            .replace(/\{date\}/g, item.timeago)
                            .replace(/\{avatar\}/g, item.avatar)
                            .replace(/\{message\}/g, item.message);

                if(item.opened) msg = msg.replace('"recipient"', '"recipient opened"');
                else if(item.sent) msg = msg.replace('"recipient"', '"recipient sent"');

                $list.append(msg);
              });
            } else {
              // error
            }
          });

          txt = '<span class="label label-lilac">' + user_txt + '</span>';
          reward_id = '';
          others_id = '';
        } else if(!reward_id && !others_id) {
            txt = '<span class="label label-lilac">' + texts.all + '</span>';
        } else {
            user_id = '';
            if(reward_id) {
                txt = texts.reward.replace('%s', '<span class="label label-lilac">' + reward_txt + '</span>');
                if(others_id && $.inArray(others_id, ['drop', 'nondrop']) == -1) {
                    txt += ' ' + texts.and + ' ';
                    txt += '<span class="label label-lilac">' + texts[others_id] + '</span>';
                }
            }
            else if(others_id && texts[others_id]) {
                txt = texts.donors + ' ';
                txt += '<span class="label label-lilac">' + texts[others_id] + '</span>';
            }
            else {
                txt += '<span class="label label-danger">' + texts.error + '</span>';
            }
        }
        if(!private) {
            txt += ' - <span class="badge">' + texts.total.replace('%s', total_users) + '</span>';
        }

        $('.ajax-message input[name="reward"]').val(reward_id || '');
        $('.ajax-message input[name="filter"]').val(others_id || '');
        $('.ajax-message input[name="users"]').val(user_id || '');
        $('.ajax-message .recipients').html(prefix + ' <strong>'+ txt + '</strong>');
    });

    $(document).on('message-sent', function(evt, request, response){
        // console.log('message sent', request, response);
        $('.ajax-message input[name="reward"]').val('');
        $('.ajax-message input[name="filter"]').val('');
        $('.ajax-message input[name="users"]').val('');
        // Clear subject and body?
        $('.ajax-message input[name="subject"]').val('');
        $('.ajax-message textarea[name="body"]').val('');

        $('#messageModal').modal('hide');
        if(request.users) {
            for(var i in request.users) {
                // console.log(request.users[i]);
                var $span = $('a.send-private[data-user="' +  request.users[i] + '"]>span');
                $span.text(parseInt($span.text()) + 1);
            }
        }
    });
})

// @license-end
</script>

<?php $this->append() ?>

