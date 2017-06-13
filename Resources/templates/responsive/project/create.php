<?php


$this->layout('layout', [
    'bodyClass' => 'project',
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
                    <input class="form-control" name="name" value="" type="text" required>
                </div>
                <div class="form-group col-sm-12" id="subtitle-group">
                    <div class="alert alert-success">
                        <?= $this->text('project-create-subtitle-alert') ?>
                    </div>
                    <label><?= $this->text('overview-field-subtitle') ?></label>
                    <input class="form-control" name="subtitle" value="" type="text" required>
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
                            <input type="text" class="form-control minimum" name="minimum" id="minimum" value="" required>
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
                <div class="form-group col-sm-12" id="calendar-group">
                    <label><?= $this->text('project-create-publishing-date-field') ?></label>
                    <div id="calendar" >
                    </div>
                    <input type="hidden" name="publishing_date" id="publishing-date" value="" required>
                </div>

                <div class="form-group col-sm-12" id="social-group">
                    <div class="alert alert-success">
                        <?= $this->text('project-create-social-select-alert') ?>
                    </div>
                    <label><?= $this->text('project-create-social-select-field') ?></label>
                    <div class="row">
                        <div class="col-xs-6 col-sm-4 pay-method text-left-important">
                            <label class="category" for="no-social">
                                <input class="social-category" id="no-social" name="social" value="0" type="radio" required>
                                <img class="img-responsive img-method align-center-margin" alt="<?= $category->name ?>" title="none" src="/assets/img/project/create/none.png">
                                <span class="method-text">
                                <?= $this->text('project-create-no-social-commitment') ?>
                                </span>
                            </label>
                        </div>
                    <?php foreach($this->social_commitments as $key => $social): ?>
                        <div class="col-xs-6 col-sm-4 pay-method text-left-important <?= ($key+1)%3==0 ? 'clear-both-md clear-both-sm' : '' ?> <?= ($key+1)%2==0 ? 'clear-both-xs' : '' ?>" >
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
                    <input class="form-control" name="social-description" value="" required>
                </div>
                <div class="form-group col-sm-12" id="accept-group" >
                    <label class="terms">
                        <input class="" type="checkbox" name="create-accept" id="create-accept" value="1">
                            <?= $this->text('project-create-terms-field') ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-md-offset-3 col-sm-6 spacer">
                <button id="create-continue" disabled="disabled" type="submit" class="btn btn-block green"><?= $this->text('project-create-save-button') ?></button>
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
        <h4 class="modal-title" id="myModalLabel"><?= $terms ? $terms->description : 'Howto page is missing!' ?></h4>
      </div>
      <div class="modal-body">
        <?= $terms ? $terms->parseContent() : 'Please create a howto page.' ?>
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

        var _get_ajax_invest_result = function() {
            var minimum=$("#minimum").val();

            $.ajax({
                url: "/project/investors-required",
                data: { 'minimum' : minimum },
                type: 'post',
                success: function(result){
                    $("#investors-number").html(result);
                }
            });

        };

        $('#create-accept').change(function() {
            var social_selected=$("input[name=social]:checked").val();

            //If none selected in social radio button, continue button is always disabled
            if(social_selected==0)
                $('#create-continue').attr('disabled', true);
            else
                $('#create-continue').attr('disabled', !this.checked);

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

        $("#project-form").on('click', 'div.form-group', function(){
            $(".form-group").removeClass("active");
            $(this).addClass("active");

            var item_id=$(this).attr('id');

            $("#"+item_id+" input").focus();

            $("#"+item_id+" .alert").fadeIn(1000);

            $('html, body').animate({
              scrollTop: ($("#"+item_id).offset().top)
              },500);

        });

        $("#project-form").on('keypress', "div.form-group input", function (e) {
            console.log(e.keyCode);
            if (e.keyCode == 10 || e.keyCode == 13 || e.keyCode == 9) {
                e.preventDefault();
                var item_id=$(this).closest("div.form-group").attr('id');
                var next=$('#'+item_id).next("div.form-group");
                var next_id=$(next).attr('id');
                console.log('next is', next_id);
                $('#'+next_id).click();

                return false;
            }
        });

        $('#calendar').datetimepicker({
            inline: true,
            format: 'MM/DD/YYYY',
            locale: '<?= $this->lang_current() ?>'
            }).on('dp.change', function (e) {
                $('#publishing-date').val(e.date.format('YYYY/MM/DD'));
                $('#calendar-group').addClass("active");
        });

        $("form").submit(function(e){
            var form = this;
            e.preventDefault();
            $("#submit-alert .alert").fadeIn(1000);
            $('html, body').animate({
              scrollTop: ($("#submit-alert").offset().top)
              },500);
            setTimeout(function () {
                    form.submit();
            }, 3000); // in milliseconds

        });

    });

</script>
<?php $this->append() ?>


