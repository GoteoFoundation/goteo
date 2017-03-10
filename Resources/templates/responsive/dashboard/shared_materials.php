<?php $this->layout('dashboard/layout') ?>
<?php $this->section('dashboard-content') ?>
    <div class="container general-dashboard spacer">
        <h2><?= $this->text('project-menu-home') ?></h2>
        <?php if($this->projects): ?>
        <form id="selector-form" name="selector_form" action="<?php echo '/dashboard/projects/'.$this->section.'/select'; ?>" method="post">
            <select id="selector" name="project" onchange="document.getElementById('selector-form').submit();">
            <?php foreach ($this->projects as $project) : ?>
                <option value="<?php echo $project->id; ?>"<?php if ($project->id == $_SESSION['project']) echo ' selected="selected"'; ?> ><?php echo $project->name; ?></option>
            <?php endforeach; ?>
            </select>
        </form>
        <?php else : ?>
        <p><?= $this->text('dashboard-no-projects') ?></p>
        <?php endif; ?>
    </div>

    <div class="container general-dashboard">
        <h1><?= $this->text('project-share-materials') ?></h1>
        <?php if($this->project->status==4||$this->project->status==5): ?>
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
                <?= $this->insert('dashboard/partials/shared_materials/new_material_form') ?>
            </div>
        <?php if($this->project->social_rewards): ?>
        <div id="materials-table">
        <?= $this->insert('dashboard/partials/shared_materials/materials_table.php') ?>
        </div>
        <?php else: ?>
        <h3><?= $this->text('project-no-share-material') ?></h3>
        <?php endif; ?>

    </div>

<?= $this->insert('dashboard/partials/shared_materials/save_url_modal') ?>
<?= $this->insert('dashboard/partials/shared_materials/save_url_modal_content') ?>

   
<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
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
                $.ajax({
                    url: "/dashboard/projects/icon-licenses",
                    data: { 'icon' : icon   },
                    type: 'post',
                    success: function(result){
                        $("#new-material-license").html(result);
                    }
                });
            }
        })

        $("#new-material-form").on('click', '#btn-save-new-material', function(){
           _get_ajax_save_new_material_result();
        });

    });
</script>

<?php $this->append() ?>
