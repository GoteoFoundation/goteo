<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;


$call = $this['call'];

$banners = array();
$sponsors = array();

foreach ($call->banners as $banner) {

    // a ver si es el que estamos editando o no
    if (!empty($this["banner-{$banner->id}-edit"])) {

        // a este grupo le ponemos estilo de edicion
        $banners["banner-{$banner->id}"] = array(
                'type'      => 'group',
                'class'     => 'banner editbanner',
                'children'  => array(
                    "banner-{$banner->id}-edit" => array(
                        'type'      => 'hidden',
                        'value'     => '1'
                    ),
                    "banner-{$banner->id}-id" => array(
                        'type'      => 'hidden',
                        'value'     => $banner->id
                    ),
                    "banner-{$banner->id}-order" => array(
                        'type'      => 'hidden',
                        'value'     => $banner->order
                    ),
                    "banner-{$banner->id}-name" => array(
                        'title'     => Text::get('call-field-banner-name'),
                        'type'      => 'textbox',
                        'class'     => 'inline',
                        'value'     => $banner->name,
                        'hint'      => Text::get('tooltip-call-banner-name')
                    ),
                    "banner-{$banner->id}-url" => array(
                        'title'     => Text::get('call-field-banner-url'),
                        'type'      => 'textbox',
                        'class'     => 'inline',
                        'value'     => $banner->url,
                        'hint'      => Text::get('tooltip-call-banner-url')
                    ),

                    "banner-{$banner->id}-image" => array(
                        'type'      => 'group',
                        'title'     => Text::get('call-field-banner-image'),
                        'hint'      => Text::get('tooltip-call-banner-image'),
                        'class'     => 'image',
                        'children'  => array(
                            "banner-{$banner->id}-image_upload"    => array(
                                'type'  => 'file',
                                'label' => Text::get('form-image_upload-button'),
                                'class' => 'inline image_upload',
                                'hint'  => Text::get('tooltip-call-banner-image'),
                            ),
                            "banner-{$banner->id}-image-current" => array(
                                'type' => 'hidden',
                                'value' => is_object($banner->image) ? $banner->image->id : '',
                            ),
                            "banner-{$banner->id}-image-image" => array(
                                'type'  => 'html',
                                'class' => 'inline image-image',
                                'html'  => is_object($banner->image) ?
                                           '<img src="'.$banner->image->getLink(270, 100) . '" alt="Imagen" /><button class="image-remove" type="submit" name="banner-'.$banner->id.'-image_remove" title="Quitar imagen" value="remove">X</button>' :
                                           ''
                            )

                        )
                    ),

                    "banner-{$banner->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "banner-{$banner->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            ),
                            "banner-{$banner->id}-remove" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove weak'
                            )
                        )
                    )
                )
            );
    } else {

        $banners["banner-{$banner->id}"] = array(
            'class'     => 'banner',
            'view'      => 'call/edit/banners/banner.html.php',
            'data'      => array('banner' => $banner),
        );

    }

}

foreach ($call->sponsors as $sponsor) {

    // a ver si es el que estamos editando o no
    if (!empty($this["sponsor-{$sponsor->id}-edit"])) {

        // a este grupo le ponemos estilo de edicion
        $sponsors["sponsor-{$sponsor->id}"] = array(
                'type'      => 'group',
                'class'     => 'sponsor editsponsor',
                'children'  => array(
                    "sponsor-{$sponsor->id}-edit" => array(
                        'type'      => 'hidden',
                        'value'     => '1'
                    ),
                    "sponsor-{$sponsor->id}-order" => array(
                        'type'      => 'hidden',
                        'value'     => $sponsor->order
                    ),
                    "sponsor-{$sponsor->id}-name" => array(
                        'title'     => Text::get('supports-field-sponsor-name'),
                        'type'      => 'textbox',
                        'class'     => 'inline',
                        'value'     => $sponsor->name,
                        'hint'      => Text::get('tooltip-call-sponsor-name')
                    ),
                    "sponsor-{$sponsor->id}-url" => array(
                        'title'     => Text::get('supports-field-sponsor-url'),
                        'type'      => 'textbox',
                        'class'     => 'inline',
                        'value'     => $sponsor->url,
                        'hint'      => Text::get('tooltip-call-sponsor-url')
                    ),

                    // imagen
                    "sponsor-{$sponsor->id}-image" => array(
                        'type'      => 'group',
                        'title'     => Text::get('call-field-sponsor-image'),
                        'hint'      => Text::get('tooltip-call-sponsor-image'),
                        'class'     => 'image',
                        'children'  => array(
                            "sponsor-{$sponsor->id}-image_upload"    => array(
                                'type'  => 'file',
                                'label' => Text::get('form-image_upload-button'),
                                'class' => 'inline image_upload',
                                'hint'  => Text::get('tooltip-call-sponsor-image'),
                            ),
                            "sponsor-{$sponsor->id}-image-current" => array(
                                'type' => 'hidden',
                                'value' => is_object($sponsor->image) ? $sponsor->image->id : '',
                            ),
                            "sponsor-{$sponsor->id}-image-image" => array(
                                'type'  => 'html',
                                'class' => 'inline image-image',
                                'html'  => is_object($sponsor->image) ?
                                           '<img src="'.$sponsor->image->getLink(100, 100) . '" alt="Imagen" /><button class="image-remove" type="submit" name="sponsor-'.$sponsor->id.'-image_remove" title="Quitar imagen" value="remove">X</button>' :
                                           ''
                            )

                        )
                    ),

                    "sponsor-{$sponsor->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "sponsor-{$sponsor->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            ),
                            "sponsor-{$sponsor->id}-remove" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove weak'
                            )
                        )
                    )
                )
            );

    } else {

        $sponsors["sponsor-{$sponsor->id}"] = array(
            'class'     => 'sponsor',
            'view'      => 'call/edit/sponsors/sponsor.html.php',
            'data'      => array('sponsor' => $sponsor),
        );

    }
}

$sfid = 'sf-call-supports';

echo SuperForm::get(array(

    'id'            => $sfid,
    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('call-supports-main-header'),
    'hint'          => Text::get('guide-call-supports'),
    'class'         => 'aqua',
    'elements'      => array(
        'process_supports' => array (
            'type' => 'hidden',
            'value' => 'supports'
        ),

        'tweet' => array(
            'type'      => 'textbox',
            'title'     => Text::get('call-field-tweet'),
            'hint'      => Text::get('tooltip-call-tweet'),
            'value'     => $call->tweet
        ),

        'fbappid' => array(
            'type'      => 'textbox',
            'title'     => Text::get('call-field-fbappid'),
            'hint'      => Text::get('tooltip-call-fbappid'),
            'value'     => $call->fbappid
        ),

        'banners' => array(
            'type'      => 'group',
            'title'     => 'Banners',
            'hint'      => Text::get('tooltip-call-banners'),
            'class'     => 'banner',
            'children'  => $banners + array(
                'banner-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add banner-add red',
                )
            )
        ),

        'sponsors' => array(
            'type'      => 'group',
            'title'     => 'Sponsors',
            'hint'      => Text::get('tooltip-call-sponsors'),
            'class'     => 'sponsor',
            'children'  => $sponsors + array(
                'sponsor-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add sponsor-add red',
                )
            )
        ),

        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('project/edit/errors.html.php', array(
                        'call'   => $call,
                        'step'      => $this['step']
                    ))
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-'.$this['next'],
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )
        )

    )

));
?>
<script type="text/javascript">
$(function () {

    /* banners buttons */
    var banners = $('div#<?php echo $sfid ?> li.element#li-banners');

    banners.delegate('li.element.banner input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        banners.superform({data:data});
    });

    banners.delegate('li.element.editbanner input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        banners.superform({data:data});
    });

    banners.delegate('li.element.editbanner input.remove, li.element.banner input.remove', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        banners.superform({data:data});
    });

    banners.delegate('#li-banner-add input', 'click', function (event) {
       event.preventDefault();
       var data = {};
       data[this.name] = '1';
       banners.superform({data:data});
    });

    /* sponsors buttons */
    var sponsors = $('div#<?php echo $sfid ?> li.element#li-sponsors');

    sponsors.delegate('li.element.sponsor input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        sponsors.superform({data:data});
    });

    sponsors.delegate('li.element.editsponsor input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        sponsors.superform({data:data});
    });

    sponsors.delegate('li.element.editsponsor input.remove, li.element.sponsor input.remove', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        sponsors.superform({data:data});
    });

    sponsors.delegate('#li-sponsor-add input', 'click', function (event) {
       event.preventDefault();
       var data = {};
       data[this.name] = '1';
       sponsors.superform({data:data});
    });

});
</script>
