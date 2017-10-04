<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->text('rewards-main-header') ?></h1>
    <p><?= $this->text('guide-project-rewards') ?></p>

    <?= $this->supply('dashboard-content-form', function() {
        $form = $this->raw('form');
        echo $this->form_start($form);

        // echo $this->form_row($form['title-rewards']);

        echo '<div class="reward-list">';
        foreach($this->rewards as $reward) {
            echo $this->insert('dashboard/project/partials/reward_item', ['reward' => $reward, 'form' => $form]);
        }
        echo '</div>';
        echo '<div class="form-group">'.$this->form_row($form['add-reward']).'</div>';


        echo $this->form_end($form);

    }) ?>

  </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){

    // Send the form via AJAX
    $('.autoform').on('click', '.add-reward', function(e){
        e.preventDefault();
        var $form = $(this).closest('form');
        var $list = $form.find('.reward-list');
        var serial = $form.serialize() + '&' + encodeURIComponent($(this).attr('name')) + '=';
        console.log('add reward', serial);

        $but = $(this).hide();
        $list.find('>.text-danger').remove();
        $list.append('<div class="loading"></div>');
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: serial
        }).done(function (data) {
            var $data = $(data);
            $list.append($data.hide());
            $data.slideDown();
        }).fail(function (data) {
            $list.append('<p class="text-danger">' + data + '</p>');
        }).always(function() {
            $but.show();
            $list.find('>.loading').remove();
        });
    });

    $('form.autoform').on('click', '.remove-reward', function(e){
        if(e.isPropagationStopped()) return false;
        e.preventDefault();
        var $form = $(this).closest('form');
        var $list = $form.find('.reward-list');
        var serial = $form.serialize() + '&' + encodeURIComponent($(this).attr('name')) + '=';
        var $item = $(this).closest('.panel');
        $(this).replaceWith('<div class="loading"></div>');
        $item.find(':input').attr('disabled', true);
        console.log('del reward', serial);
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: serial
        }).done(function () {
            $item.slideUp(function(){
                $(this).remove();
            });
        }).fail(function (data) {
            console.log('An error occurred.', data);
            alert(data.responseText);
        });
    });

});

// @license-end
</script>
<?php $this->append() ?>
