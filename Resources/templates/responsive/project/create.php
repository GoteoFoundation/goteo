<?php


$this->layout('layout', [
    'bodyClass' => 'project create',
    'title' => $this->text('meta-title-create-project'),
    'meta_description' => $this->text('meta-description-create-project')
    ]);

$this->section('content');

$terms=$this->terms;

?>

<div class="container create-form" >
    <div class="row">
        <div class="col-md-6 col-sm-offset-3">
            <h1><?= $this->text('project-create-title') ?></h1>
        </div>
    </div>
    <form action="" id="project-form" class="create-project-form" method="post">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="form-group col-sm-12 active" id="name-group">
                    <label><?= $this->text('overview-field-name') ?></label>
                    <input autocomplete="off" tabindex="0" class="form-control" name="name" value="" type="text" required>
                </div>
                <div class="form-group col-sm-12" id="subtitle-group">
                    <div class="alert alert-success">
                        <?= $this->text('project-create-subtitle-alert') ?>
                    </div>
                    <label><?= $this->text('overview-field-subtitle') ?></label>
                    <input autocomplete="off" tabindex="0" class="form-control" name="subtitle" value="" type="text" required>
                </div>
                <div class="form-group col-sm-12" id="minimum-group">
                    <div class="alert alert-success">
                        <div id="default-msg" >
                        <?= $this->text('project-create-minimum-alert') ?>
                        </div>
                        <div id="calculated-msg" >
                        <?= $this->text('project-create-minimum-calculated-alert') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label><?= $this->text('project-create-minimum-field') ?></label>
                            <input tabindex="0" type="text" class="form-control minimum" name="minimum" id="minimum" value="" required>
                        </div>
                        <div class="col-sm-4">
                            <button id="calculate-invest" type="submit" class="btn btn-block pink"><?= $this->text('project-create-minimum-calculate-button') ?></button>
                        </div>
                        <div class="col-sm-4">
                            <div class="investors spacer-20" id="investors" >
                                <i class="fa fa-users"></i>
                                <span id="investors-number" ></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div tabindex="0" class="form-group col-sm-12" id="calendar-group" style="outline:none">
                    <label><?= $this->text('project-create-publishing-date-field') ?></label>
                    <div id="calendar">
                    </div>
                    <input type="hidden" name="publishing_date" id="publishing-date" value="" required>
                </div>

                <div class="form-group col-sm-12" id="social-group">
                    <div class="alert alert-success">
                        <?= $this->text('project-create-social-select-alert') ?>
                    </div>
                    <label><?= $this->text('project-create-social-select-field') ?></label>
                    <div class="row">
                        <div class="col-xs-6 col-sm-4 social-commitment-option text-left-important">
                            <label class="category" for="no-social">
                                <input tabindex="0" class="social-category" id="no-social" name="social" value="0" type="radio" required>
                                <img class="img-responsive img-method align-center-margin" alt="<?= $category->name ?>" title="none" src="/assets/img/project/create/none.png">
                                <span class="method-text">
                                <?= $this->text('project-create-no-social-commitment') ?>
                                </span>
                            </label>
                        </div>
                        <?php foreach($this->social_commitments as $key => $social): ?>
                        <div class="col-xs-6 col-sm-4 social-commitment-option text-left-important<?= ($key+1)%3==0 ? ' clear-both-md clear-both-sm' : '' ?><?= ($key+1)%2==0 ? ' clear-both-xs' : '' ?>">
                            <label class="category" for="<?= $key ?>-social">
                                <input class="social-category" name="social" id="<?= $key ?>-social" value="<?= $social->id ?>" type="radio" required>
                                <img class="img-responsive img-method align-center-margin" alt="<?= $social->name ?>" title="<?= $social->description ?>" src="<?= $social->image->getLink(60, 60, false) ?>">
                                 <span class="method-text">
                                <?= $social->name ?>
                                </span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group col-sm-12" id="description-group">
                    <div class="alert alert-success">
                        <?= $this->text('project-create-social-text-alert') ?>
                    </div>
                    <label><?= $this->text('project-create-social-text-field') ?></label>
                    <input autocomplete="off" tabindex="0" class="form-control" name="social-description" value="" required>
                </div>
                <div class="form-group col-sm-12" id="accept-group" >
                    <label class="terms">
                        <input tabindex="0" class="" type="checkbox" name="create-accept" id="create-accept" value="1">
                            <?= $this->text('project-create-terms-field') ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-md-offset-3 col-sm-6 spacer">
                <button tabindex="0" id="create-continue" disabled="disabled" type="submit" class="btn btn-block green"><?= $this->text('project-create-save-button') ?></button>
            </div>
        </div>
        <div class="row" id="submit-alert">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="alert alert-success">
                <?= $this->text('project-create-save-alert') ?>
                </div>
            </div>
        </div>
    </form>

</div>

<!-- End container fluid -->

<!-- Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $terms->description ?></h4>
      </div>
      <div class="modal-body">
        <?= $terms->parseContent() ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="noSocialModal" tabindex="-1" role="dialog" aria-labelledby="noSocialLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="noSocialLabel"><?= $this->text('project-create-modal-no-social-title') ?></h4>
      </div>
      <div class="modal-body">
        <p><?= $this->text('project-create-modal-no-social-description') ?></p>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">

    $(function(){

        $('#calendar').datetimepicker({
            inline: true,
            format: 'MM/DD/YYYY',
            locale: '<?= $this->lang_current() ?>',
            keyBinds: {
                up: null,
                down: function (widget) {
                    if (!widget) {
                        this.show();
                        return;
                    }
                },
                left: null,
                right: null,
                t : null,
                'delete' : null
            }
            }).on('dp.change', function (e) {
                _activate_calendar();
                $('#publishing-date').val(e.date.format('YYYY/MM/DD'));
        });

        var _get_ajax_invest_result = function() {
            var minimum=$("#minimum").val();

            $.ajax({
                url: "/api/stats/investors-required",
                data: { 'minimum' : minimum },
                type: 'post',
                success: function(result){
                    $("#investors-number").html(result);
                }
            });

        };

        $('#project-form input[type="text"]:first').focus();

        $('#create-accept').change(function() {
            var social_selected = $('input[name="social"]:checked').val();

            //If none selected in social radio button, continue button is always disabled
            if(social_selected==0)
                $('#create-continue').attr('disabled', true);
            else
                $('#create-continue').attr('disabled', !this.checked);

        });

        $('#noSocialModal').on('hidden.bs.modal', function (e) {
            $('input[name="social"]:first').focus();

        });

        $('input.social-category').change(function() {
            var selected=$("input[name=social]:checked").val();
            var accept_terms=$('#create-accept').prop('checked');
            //If selected is zero (value for none) show modal and disabled button
            if(selected==0)
            {
                $('#create-continue').attr('disabled', true);
                $("#noSocialModal").modal("show");
            }
            else if(accept_terms)
                $('#create-continue').attr('disabled', false);

        });

        $("#project-form").on('click', '#calculate-invest', function(){

           _get_ajax_invest_result();

           $("#investors").show(500);

           $("#default-msg").hide(700);
           $("#calculated-msg").show(700);

           return false;

        });

        $("#calendar-group").on('keypress', function(e) {

            var calendar = $('#calendar').data("DateTimePicker");
            var first = $('#calendar').find(".datepicker-days tr td:first");
            var last = $('#calendar').find(".datepicker-days tr td:last");
            var new_date = null;
            switch(e.keyCode) {
                case 37: // left
                    new_date = calendar.date().clone().subtract(1, 'd');
                    break;
                case 38: // up
                    new_date = calendar.date().clone().subtract(7, 'd');
                    break;
                case 39: // right
                    new_date = calendar.date().clone().add(1, 'd');
                    break;
                case 40: // Down
                    new_date = calendar.date().clone().add(7, 'd');
                    break;
                case 10:
                case 13:
                case 9:
                    // Goto next
                    $(this).blur();
                    $(this).next('div.form-group').click();
                    break;
            }
            if(new_date) {
                e.preventDefault();
                calendar.date(new_date);
                // Datepicker does'nt seem to update the UI
                // if you dont hide/show when changing months
                calendar.hide().show();
            }
        });

        var _activate_calendar = function() {
            if(!$('#calendar-group').hasClass('active')) {
                $('#calendar-group').addClass('active');
                $('#calendar-group').focus();
            }
        };

        $("#project-form").on('click', 'div.form-group', function(){

            var item_id = $(this).attr('id');

            if(item_id === 'calendar-group')
                _activate_calendar();

            $(".form-group").removeClass("active");
            $(this).addClass("active");

            if($("#"+item_id+' input').length)
                $("#"+item_id+" input:first").focus();

            $("#"+item_id+" .alert").fadeIn(1000);

            $('html, body').animate({
              scrollTop: ($("#"+item_id).offset().top)
              },500);

        });

        $("#project-form").on('keypress', 'div.form-group input', function (e) {
            if (e.keyCode == 10 || e.keyCode == 13 || e.keyCode == 9) {
                e.preventDefault();
                $(this).blur();
                $(this).closest("div.form-group").next('div.form-group').click();
            }
            if($(this).is('[type="radio"]')) {
                var el = null;
                switch(e.keyCode) {
                    case 37: // left
                    case 38: // up
                        el = $(this).closest('.social-commitment-option').prev('.social-commitment-option').find('input[type="radio"]');
                        if(!el.length) {
                            el = $('input[name="social"]:last');
                        }
                        break;
                    case 39: // right
                    case 40: // Down
                        el = $(this).closest('.social-commitment-option').next('.social-commitment-option').find('input[type="radio"]');
                        if(!el.length) {
                            el = $('input[name="social"]:first');
                        }
                        break;
                }
                if(el) {
                    e.preventDefault();
                    el.focus();
                }
            }
        });


        $("form").submit(function(e){
            var form = this;
            e.preventDefault();
            $('html, body').animate({
              scrollTop: ($("#submit-alert").offset().top)
              },500);
            $("#submit-alert .alert").fadeIn(1000, function() {
                form.submit();
            });
        });

    });

</script>
<?php $this->append() ?>


