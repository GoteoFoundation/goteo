<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$project = $this['project'];
$user = $this['user'];

$interests = array();

$errors = $project->errors[$this['step']] ?: array();
$okeys  = $project->okeys[$this['step']] ?: array();

foreach ($this['interests'] as $value => $label) {
    $interests[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $user->interests)
        );
}

$user_webs = array();

foreach ($user->webs as $web) {

    $ch = array();

    // a ver si es el que estamos editando o no
    if (!empty($this["web-{$web->id}-edit"])) {

        $user_webs["web-{$web->id}"] = array(
            'type'      => 'group',
            'class'     => 'web editweb',
            'children'  => array(
                    "web-{$web->id}-edit" => array(
                        'type'  => 'hidden',
                        'class' => 'inline',
                        'value' => '1'
                    ),
                    'web-' . $web->id . '-url' => array(
                        'type'      => 'textbox',
                        'required'  => true,
                        'title'     => Text::get('profile-field-url'),
                        'value'     => $web->url,
                        'hint'      => Text::get('tooltip-user-webs'),
                        'errors'    => !empty($errors['web-' . $web->id . '-url']) ? array($errors['web-' . $web->id . '-url']) : array(),
                        'ok'        => !empty($okeys['web-' . $web->id . '-url']) ? array($okeys['web-' . $web->id . '-url']) : array(),
                        'class'     => 'web-url inline'
                    ),
                    "web-{$web->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'inline buttons',
                        'children' => array(
                            "web-{$web->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            ),
                            "web-{$web->id}-remove" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove weak'
                            )
                        )
                    )
                )
        );

    } else {

        $user_webs["web-{$web->id}"] = array(
            'class'     => 'web',
            'view'      => 'project/edit/webs/web.html.php',
            'data'      => array('web' => $web),
        );

    }

}

$sfid = 'sf-project-profile';

echo SuperForm::get(array(
    'id'            => $sfid,
    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('profile-main-header'),
    'hint'          => Text::get('guide-project-user-information'),
    'elements'      => array(
        'process_userProfile' => array (
            'type' => 'hidden',
            'value' => 'userProfile'
        ),

        'anchor-profile' => array(
            'type' => 'html',
            'html' => '<a name="profile"></a>'
        ),

        'user_name' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => Text::get('profile-field-name'),
            'hint'      => Text::get('tooltip-user-name'),
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array(),
            'value'     => $user->name
        ),
        'user_location' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => Text::get('profile-field-location'),
            'hint'      => Text::get('tooltip-user-location'),
            'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
            'ok'        => !empty($okeys['location']) ? array($okeys['location']) : array(),
            //google autocomplete
            'class'     => 'geo-autocomplete',
            //write data to location tables
            'data'      => array('geocoder-type' => 'user'),
            'value'     => $user->location
        ),

        'anchor-avatar' => array(
            'type' => 'html',
            'class' => 'inline',
            'html' => '<a name="avatar"></a>'
        ),

        'user_avatar' => array(
            'type'      => 'group',
            'required'  => true,
            'title'     => Text::get('profile-fields-image-title'),
            'hint'      => Text::get('tooltip-user-image'),
            'errors'    => !empty($errors['avatar']) ? array($errors['avatar']) : array(),
            'ok'        => !empty($okeys['avatar']) ? array($okeys['avatar']) : array(),
            'class'     => 'user_avatar',
            'children'  => array(
                'avatar_upload'    => array(
                    'type'  => 'file',
                    'label' => Text::get('form-image_upload-button'),
                    'class' => 'inline avatar_upload',
                    'hint'  => Text::get('tooltip-user-image'),
                    'onclick' => "document.getElementById('proj-superform').action += '#avatar';"
                ),
                'avatar-current' => array(
                    'type' => 'hidden',
                    'value' => $user->avatar->id == 'la_gota.png' ? '' : $user->avatar->id,
                ),
                'avatar-image' => array(
                    'type'  => 'html',
                    'class' => 'inline avatar-image',
                    'html'  => is_object($user->avatar) &&  $user->avatar->id != 'la_gota.png' ?
                               $user->avatar . '<img src="' . SITE_URL . '/image/' . $user->avatar->id . '/128/128" alt="Avatar" /><button class="image-remove" type="submit" name="avatar-'.$user->avatar->hash.'-remove" title="Quitar imagen" value="remove">X</button>' :
                               ''
                )

            )
        ),

        'user_about' => array(
            'type'      => 'textarea',
            'required'  => true,
            'cols'      => 40,
            'rows'      => 4,
            'title'     => Text::get('profile-field-about'),
            'hint'      => Text::get('tooltip-user-about'),
            'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
            'ok'        => !empty($okeys['about']) ? array($okeys['about']) : array(),
            'value'     => $user->about
        ),
        'interests' => array(
            'type'      => 'checkboxes',
            'required'  => true,
            'class'     => 'cols_3',
            'name'      => 'user_interests[]',
            'title'     => Text::get('profile-field-interests'),
            'hint'      => Text::get('tooltip-user-interests'),
            'errors'    => !empty($errors['interests']) ? array($errors['interests']) : array(),
            'ok'        => !empty($okeys['interests']) ? array($okeys['interests']) : array(),
            'options'   => $interests
        ),

        'anchor-webs' => array(
            'type' => 'html',
            'html' => '<a name="webs"></a>'
        ),

        'user_webs' => array(
            'type'      => 'group',
            'required'  => true,
            'title'     => Text::get('profile-field-websites'),
            'hint'      => Text::get('tooltip-user-webs'),
            'class'     => 'webs',
            'errors'    => !empty($errors['webs']) ? array($errors['webs']) : array(),
            'ok'        => !empty($okeys['webs']) ? array($okeys['webs']) : array(),
            'children'  => $user_webs + array(
                'web-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add red'
                )
            )
        ),
        'user_social' => array(
            'type'      => 'group',
            'title'     => Text::get('profile-fields-social-title'),
            'children'  => array(
                'user_facebook' => array(
                    'type'      => 'textbox',
                    'class'     => 'facebook',
                    'size'      => 40,
                    'title'     => Text::get('regular-facebook'),
                    'hint'      => Text::get('tooltip-user-facebook'),
                    'errors'    => !empty($errors['facebook']) ? array($errors['facebook']) : array(),
                    'ok'        => !empty($okeys['facebook']) ? array($okeys['facebook']) : array(),
                    'value'     => empty($user->facebook) ? Text::get('regular-facebook-url') : $user->facebook
                ),
                'user_google' => array(
                    'type'      => 'textbox',
                    'class'     => 'google',
                    'size'      => 40,
                    'title'     => Text::get('regular-google'),
                    'hint'      => Text::get('tooltip-user-google'),
                    'errors'    => !empty($errors['google']) ? array($errors['google']) : array(),
                    'ok'        => !empty($okeys['google']) ? array($okeys['google']) : array(),
                    'value'     => empty($user->google) ? Text::get('regular-google-url') : $user->google
                ),
                'user_twitter' => array(
                    'type'      => 'textbox',
                    'class'     => 'twitter',
                    'size'      => 40,
                    'title'     => Text::get('regular-twitter'),
                    'hint'      => Text::get('tooltip-user-twitter'),
                    'errors'    => !empty($errors['twitter']) ? array($errors['twitter']) : array(),
                    'ok'        => !empty($okeys['twitter']) ? array($okeys['twitter']) : array(),
                    'value'     => empty($user->twitter) ? Text::get('regular-twitter-url') : $user->twitter
                ),
                'user_identica' => array(
                    'type'      => 'textbox',
                    'class'     => 'identica',
                    'size'      => 40,
                    'title'     => Text::get('regular-identica'),
                    'hint'      => Text::get('tooltip-user-identica'),
                    'errors'    => !empty($errors['identica']) ? array($errors['identica']) : array(),
                    'ok'        => !empty($okeys['identica']) ? array($okeys['identica']) : array(),
                    'value'     => empty($user->identica) ? Text::get('regular-identica-url') : $user->identica
                ),
                'user_linkedin' => array(
                    'type'      => 'textbox',
                    'class'     => 'linkedin',
                    'size'      => 40,
                    'title'     => Text::get('regular-linkedin'),
                    'hint'      => Text::get('tooltip-user-linkedin'),
                    'errors'    => !empty($errors['linkedin']) ? array($errors['linkedin']) : array(),
                    'ok'        => !empty($okeys['linkedin']) ? array($okeys['linkedin']) : array(),
                    'value'     => empty($user->linkedin) ? Text::get('regular-linkedin-url') : $user->linkedin
                )
            )
        ),
        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('project/edit/errors.html.php', array(
                        'project'   => $project,
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

    var webs = $('div#<?php echo $sfid ?> li.element#li-user_webs');

    webs.delegate('li.element.web input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        webs.superform({data:data});
    });

    webs.delegate('li.element.editweb input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        webs.superform({data:data});
    });

    webs.delegate('li.element.editweb input.remove, li.element.web input.remove', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        webs.superform({data:data});
    });

    webs.delegate('#li-web-add input', 'click', function (event) {
       event.preventDefault();
       var data = {};
       data[this.name] = '1';
       webs.superform({data:data});
    });

});
</script>
