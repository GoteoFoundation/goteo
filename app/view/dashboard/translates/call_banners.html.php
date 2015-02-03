<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$call = $this['call'];
$errors = $this['errors'];

$banners = array();

if (!empty($call->banners)) {

    foreach ($call->banners as $banner) {

        $ch = array();

        // a ver si es el que estamos editando o no
        if (!empty($this["banner-{$banner->id}-edit"])) {

            $original = \Goteo\Model\Call\Banner::get($banner->id);

            // a este grupo le ponemos estilo de edicion
            $banners["banner-{$banner->id}"] = array(
                'type'      => 'group',
                'class'     => 'banner editbanner',
                'children'  => array(
                    "banner-{$banner->id}-banner-orig" => array(
                        'title'     => Text::get('call-field-banner-name'),
                        'type'      => 'HTML',
                        'html'      => $original->name
                    ),
                    "banner-{$banner->id}-name" => array(
                        'title'     => '',
                        'type'      => 'TextBox',
                        'size'      => 100,
                        'class'     => 'inline',
                        'value'     => $banner->name,
                        'errors'    => !empty($errors["banner-{$banner->id}-banner"]) ? array($errors["banner-{$banner->id}-banner"]) : array(),
                        'ok'        => !empty($okeys["banner-{$banner->id}-banner"]) ? array($okeys["banner-{$banner->id}-banner"]) : array(),
                        'hint'      => Text::get('tooltip-call-banner-name')
                    ),
                    "banner-{$banner->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "banner-{$banner->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            )
                        )
                    )
                )
            );

        } else {

            $banners["banner-{$banner->id}"] = array(
                'class'     => 'banner',
                'view'      => 'dashboard/translates/call_banners/call_banner.html.php',
                'data'      => array('banner' => $banner),
            );
        }


    }
}


$sfid = 'sf-call-banners';

?>

<form method="post" action="/dashboard/translates/banners/save" class="call" enctype="multipart/form-data">

<?php echo SuperForm::get(array(
    'id'            => $sfid,
    'action'        => '',
    'level'         => 3,
    'method'        => 'post',
    'title'         => '',
    'hint'          => Text::get('guide-call-supports'),
    'class'         => 'aqua',
    /*
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-banners',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    */
    'elements'      => array(
        'process_banners' => array (
            'type' => 'hidden',
            'value' => 'banners'
        ),
        'banners' => array(
            'type'      => 'group',
            'title'     => 'Banners',
            'hint'      => Text::get('tooltip-call-banners'),
            'children'  => $banners
        )
    )

));
?>
</form>
<script type="text/javascript">
$(function () {

    var banners = $('div#<?php echo $sfid ?> li.element#li-banners');

    banners.delegate('li.element.banner input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        banners.superform({data:data});
    });

    banners.delegate('li.element.editbanner input.ok', 'click', function (event) {
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        banners.superform({data:data});
        event.preventDefault();
    });

});
</script>
