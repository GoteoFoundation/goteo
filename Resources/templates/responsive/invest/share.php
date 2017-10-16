<?php

$this->layout('invest/layout', ['alt_title' => $this->text('invest-share-title')]);

$this->section('main-content');

?>

<div class="container">

    <div class="row row-form">
        <div class="panel panel-default invest-container">
            <div class="panel-body">
                <h2 class="col-sm-offset-1 col-sm-10 padding-bottom-2"><?= $this->text('invest-share-title') ?></h2>
                <div class="reminder col-sm-10 col-sm-offset-1">
                <?= $this->text('project-invest-ok') ?>
                </div>

                <div class="row">
                    <h3 class="col-sm-offset-1 col-sm-10 clearfix padding-bottom-6"><?= $this->text('project-spread-header') ?></h3>
                </div>

                <div class="row">
                    <div class="col-sm-5 col-sm-offset-1 margin-2">
                        <a href="<?= $this->facebook_url ?>" target="_blank" class="btn btn-block btn-social btn-facebook">
                            <i class="fa fa-facebook"></i> <?= $this->text('spread-facebook') ?>
                        </a>
                    </div>
                    <div class="col-sm-5 margin-2">
                        <a href="<?= $this->twitter_url ?>" target="_blank" class="btn btn-block btn-social btn-twitter">
                            <i class="fa fa-twitter"></i> <?= $this->text('spread-twitter') ?>
                        </a>
                    </div>

                </div>

                <hr class="share">

                <h3 class="col-sm-offset-1 col-sm-10 standard-margin-top padding-bottom-6" ><?= $this->text('project-messages-send_direct-header') ?></h3>

                <div class="row standard-margin-top" id="container-msg-form">
                    <form class="col-sm-10 col-sm-offset-1" name="msg-form" id="msg-form" action="">
                        <div class="alert alert-danger" role="alert" id="error" style="display:none;">

                        </div>
                        <textarea class="form-control" id="support-msg" rows="4" required></textarea>
                        <div class="margin-2 standard-margin-top">
                            <button type="button" class="btn btn-lg btn-cyan" id="send-msg" value=""><i class="fa fa-paper-plane-o"></i> <?= $this->text('project-messages-send_message-button') ?></button>
                        </div>
                    </form>
                </div>

                <!--

                <hr class="share">

                <h3 class="col-md-offset-1 standard-margin-top sm-display-none padding-bottom-6" ><?= $this->text('project-spread-widget') ?></h3>

                -->

                <!-- Widget code -->
                <!--
                <div class="row standard-margin-top sm-display-none">
                    <div class="col-md-5 col-md-offset-1">
                        <?= $this->raw('widget_code') ?>
                        <h4 class="embed-code"><?= $this->text('project-spread-embed_code') ?></h4>
                        <textarea class="widget-code" onclick="this.focus();this.select()" readonly="readonly" ><?= $this->widget_code ?></textarea>
                    </div>
                    <div class="col-md-5">
                        <?= $this->raw('widget_code_investor') ?>
                        <h4 class="embed-code"><?= $this->text('project-spread-embed_code') ?></h4>
                        <textarea class="widget-code" onclick="this.focus();this.select()" readonly="readonly" ><?= $this->widget_code_investor ?></textarea>
                    </div>
                </div>

                -->

                <hr class="share hidden-xs">

                <div class="row">

                    <div class="col-sm-6 col-sm-offset-3 margin-2">
                            <a href="<?= SITE_URL.'/project/'.$this->project->id ?>" class="text-decoration-none" >
                                <button type="button" class="btn btn-block green" value=""><?= $this->text('project-return-button') ?></button>
                            </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->insert('invest/partials/message_modal') ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    var _insert_ajax_msg = function() {
        var msg=$("#support-msg").val();

        $.ajax({
            url: "/invest/<?= $this->project->id ?>/<?= $this->invest->id ?>/support-msg",
            data: { 'msg' : msg, 'invest' : '<?= $this->invest->id ?>'  },
            type: 'post',
            success: function(result){
                if(result['result'])
                {
                    $('#messageModal').modal('show');
                    $('#error').hide();
                }
                else
                {
                    $('#error').html('<?= $this->text('regular-message_fail') ?>');
                    $('#error').show();
                }
            }
        });
   };

    $("#container-msg-form").on('click', '#send-msg', function(){
       _insert_ajax_msg();
       $("#send-msg").prop("disabled", true);
    });

});

// @license-end
</script>

<?php $this->append() ?>
