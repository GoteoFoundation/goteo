<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-project-content') ?>

    <!-- <h1><?= $this->text('project-share-materials') ?></h1> -->

    <?php if($this->allowNewShare): ?>

        <div class="row spacer">
            <div class="col-xs-6 col-sm-3 col-md-2" id="button-container" data-toggle="collapse" data-target="#new-material-form">
                <button id="add-new-material" class="btn btn-pink"><span class="glyphicon glyphicon-plus"></span> <?= $this->text('dashboard-add-new-share-material') ?></button>
            </div>
        </div>

    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div id="alert-success" class="new-material-success alert alert-success" style="display: none;">
              <strong class="msg"></strong>
            </div>
            <?= $this->insert('dashboard/project/partials/materials/new_material_form') ?>
        </div>

    <div id="materials-table">
        <?php if($this->project->social_rewards): ?>
            <?= $this->insert('dashboard/project/partials/materials/materials_table') ?>
        <?php else: ?>
            <h3><?= $this->text('project-no-share-material') ?></h3>
        <?php endif; ?>
    </div>


    <?= $this->insert('dashboard/project/partials/materials/save_url_modal') ?>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

    $(function(){

        var project = '<?= $this->project->id ?>';

        $('#materials-table').on('click', '.edit-url', function() {
            $("#edit-url").val($(this).data('url'));
            $("#reward-id").val($(this).data('reward'));
        });

        var _show_success_msg = function(msg) {
            $("#alert-success .msg").html(msg);
            $("#alert-success").fadeTo(1000, 500).slideUp(1500, function(){
                $("#alert-success").alert('close');
            });

        }
        var _update_material_table = function() {
            //update table
            $.get('/dashboard/ajax/projects/' + project + '/materials-table',
                function(table) {
                    $("#materials-table").html(table);
                });
        };

        var _get_ajax_save_url_result = function() {
            var url = $("#edit-url").val();
            var reward_id = $("#reward-id").val();

            $.ajax({
                url: '/api/projects/' + project + '/materials',
                data: {
                    'url' : url,
                    'reward' : reward_id
                },
                type: 'put',
                success: function(result){
                    $('#UrlModal').modal('toggle');
                    //update table
                    _update_material_table();
                    _show_success_msg('<?= $this->ee($this->text('dashboard-modal-url-success-msg'), 'js') ?>')

                },
                error: function(result, status) {
                    var error = result.responseText ? JSON.parse(result.responseText).error : result;
                    $('#reward-error').removeClass('hidden').html(error);
                }
            });
        };

        var _get_ajax_save_new_material_result = function() {

            var material = $("#new-material-material").val();
            var description = $("#new-material-description").val();
            var icon = $("#new-material-icon").val();
            var license = $("#new-material-license").val();
            var url = $("#new-material-url").val();

            $.ajax({
                url: '/api/projects/' + project + '/materials',
                data: { 'material' : material,
                        'description' : description,
                        'icon' : icon,
                        'license' : license,
                        'url' : url
                    },
                type: 'post',
                success: function(result){
                    //update table
                    _update_material_table();
                    $("#new-material-form").collapse('hide');
                    $('#new-material-form form').trigger("reset");
                    _show_success_msg('<?= $this->ee($this->text('dashboard-new-material-form-success'), 'js') ?>')
                }
            });
        };

        $("#UrlModal").on('keypress', "#edit-url", function (e) {
            if (e.keyCode == 10 || e.keyCode == 13) {
                e.preventDefault();
                _get_ajax_save_url_result();
                return false;
            }
        });

        $('#UrlModal').on('shown.bs.modal', function () {
            $('#UrlModal input:first').select();
        });

        $("#UrlModal").on('click', '#btn-save-url', function(){
           _get_ajax_save_url_result();
        });

        $('#UrlModal').on('hidden.bs.modal', function () {
            $("#modal-content").html($("#modal-content-reset").html());
        })

        $("#new-material-form").on('change', '#new-material-icon', function(){
            var icon=$("#new-material-icon").val();
            if(icon=="service"||icon=="other")
                $("#license-group").hide();
            else
            {
                $("#license-group").show();
                $.get("/api/licenses", { 'icon' : icon   }, function(data){
                    var html = '';
                    $.each(data, function(index, value) {
                        html += '<option value="' + value.id + '">' + value.name + '</option>';
                    });
                    $("#new-material-license").html(html);
                });
            }
        })

        $("#new-material-form").on('click', '#btn-save-new-material', function(){
           _get_ajax_save_new_material_result();
        });

    });

    // @license-end
</script>

<?php $this->append() ?>
