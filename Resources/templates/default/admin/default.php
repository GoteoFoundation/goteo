<?php $this->layout('admin/layout') ?>


<?php $this->section('admin-content') ?>

<div class="widget admin-home">Please select an option from the menu</div>

<?php $this->replace() ?>


<?php if($this->feed): //assign feed if available ?>

    <?php $this->section('admin-aside') ?>
        <?= $this->insert('admin/partials/feed') ?>
    <?php $this->replace() ?>

    <?php $this->section('footer') ?>

    <script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    jQuery(document).ready(function($) {
        $('.scroll-pane').jScrollPane({showArrows: true});

        $('.hov').hover(
          function () {
            $(this).addClass($(this).attr('rel'));
          },
          function () {
            $(this).removeClass($(this).attr('rel'));
          }
        );

    });
    // @license-end
    </script>

    <?php $this->append() ?>

<?php endif ?>

