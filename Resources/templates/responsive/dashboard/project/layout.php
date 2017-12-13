<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-sidebar-header') ?>

    <?= $this->insert('project/widgets/micro', ['project' => $this->project, 'admin' => $this->admin]) ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<div id="apply-modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= $this->text('project-send-review') ?></h4>
      </div>
      <div class="modal-body">
        <p><?= $this->markdown($this->text('project-send-review-desc')) ?></p>
        <?php if($this->errors): ?>
            <div class="text-danger"><?= implode("\n<br>\n", $this->errors) ?></div>
        <?php endif ?>

        <?= $this->form_form($this->raw('applyForm'), [], true); ?>

        </div>
    </div>
  </div>
</div>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){

    var gotoFirstError = function() {

      // Scrolls to last error if validate GET is available
      var $group = $('.form-group.has-error:first');
      if($group.length) {
        $input = $group.find('input,select,textarea[name]');
        // console.log($group, $input.attr('id'), $input.val());
        if($group.find('div.markdown').length) {
            // console.log('MD',form.markdowns[$input.attr('id')]);
            form.markdowns[$input.attr('id')].codemirror.focus();
        } else {
            $input.focus();
        }
        $('html, body').animate({
            scrollTop: $group.offset().top
        }, 800);
      }
    };
    $('.goto-first-error').on('click', function(e) {
        e.preventDefault();
        gotoFirstError();
    });

    // Apply project
    $('a.apply-project').on('click', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('#apply-modal').modal('show');
    });

});

// @license-end
</script>
<?php $this->append() ?>
