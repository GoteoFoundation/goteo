<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-project-content') ?>

    <!-- <h1><?= $this->text('project-share-materials') ?></h1> -->

    <?php if($this->allowNewShare): ?>

        <div class="row spacer">
            <div class="col-xs-6 col-sm-3 col-md-2" id="button-container" data-toggle="collapse" data-target="#new-material-form">
                <button id="add-new-material" class="btn btn-block side-pink"><span class="glyphicon glyphicon-plus"></span> <?= $this->text('dashboard-add-new-share-material') ?></button>
            </div>
        </div>

    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div id="alert-success" class="new-material-success alert alert-success" style="display: none;">
              <strong><?= $this->text('dashboard-new-material-form-success') ?></strong> .
            </div>
            <?= $this->insert('dashboard/project/partials/materials/new_material_form') ?>
        </div>

    <?php if($this->project->social_rewards): ?>
        <div id="materials-table">
            <?= $this->insert('dashboard/project/partials/materials/materials_table') ?>
        </div>
    <?php else: ?>
        <h3><?= $this->text('project-no-share-material') ?></h3>
    <?php endif; ?>


    <?= $this->insert('dashboard/project/partials/materials/save_url_modal') ?>
    <?= $this->insert('dashboard/project/partials/materials/save_url_modal_content') ?>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
    // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

    $(function(){

        $('#materials-table').on('click', '.edit-url', function() {
            $("#edit-url").val($(this).attr('data-value'));
            $("#reward-id").val($(this).attr('data-reward-id'));
        });

        var _update_material_table = function() {

        //update table
            $.ajax({
                url: "/dashboard/projects/update-materials-table",
                data: { 'project_id' : '<?= $this->project->id ?>'   },
                type: 'post',
                success: function(table){
                    $("#materials-table").html(table);
                }
            });
        };

        var _get_ajax_save_url_result = function() {
            var url=$("#edit-url").val();
            var reward_id=$("#reward-id").val();
            var project = '<?= $this->project->id ?>';

            $.ajax({
                url: "/dashboard/projects/save-material-url",
                data: { 'url' : url, 'reward_id' : reward_id, 'project': project  },
                type: 'post',
                success: function(result){
                    $("#modal-content").html(result);
                    //update table
                    _update_material_table();
                }
            });
        };

        var _get_ajax_save_new_material_result = function() {

            var project = '<?= $this->project->id ?>';
            var material = $("#new-material-material").val();
            var description= $("#new-material-description").val();
            var icon= $("#new-material-icon").val();
            var license= $("#new-material-license").val();
            var url= $("#new-material-url").val();

            $.ajax({
                url: "/dashboard/projects/save-new-material",
                data: { 'project' : project,
                        'material' : material,
                        'description' : description,
                        'icon' : icon,
                        'license' : license,
                        'project' : project,
                        'url' : url
                    },
                type: 'post',
                success: function(result){
                    //update table
                    _update_material_table();
                    $("#new-material-form").collapse('hide');
                    $('#new-material-form form').trigger("reset");
                    $("#alert-success").fadeTo(1000, 500).slideUp(1500, function(){
                        $("#alert-success").alert('close');
                    });
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
            $('#UrlModal input:first').focus();
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
