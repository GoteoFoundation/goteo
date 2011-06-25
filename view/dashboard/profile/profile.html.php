<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

define('ADMIN_NOAUTOSAVE', true);

$user   = $this['user'];
$errors = $this['errors'];
$this['level'] = 3;

$image = array(
    'avatar' => array(
        'type'  => 'hidden',
        'value' => $user->avatar->id,
    ),
    'avatar-image' => array(
        'type'  => 'html',
        'class' => 'inline',
        'html'  => is_object($user->avatar) ?
                   $user->avatar . '<img src="' . htmlspecialchars($user->avatar->getLink(110, 110)) . '" alt="Avatar" />' :
                   ''
    )
);

if (!empty($user->avatar) && is_object($user->avatar))
    $image ["avatar-{$user->avatar->id}-remove"] = array(
        'type'  => 'submit',
        'label' => Text::get('form-remove-button'),
        'class' => 'inline remove image-remove'
    );



$interests = array();

foreach ($this['interests'] as $value => $label) {
    $interests[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $user->interests)
        );
}

$user_webs = array();

foreach ($user->webs as $web) {

    $user_webs['web' . $web->id] = array(
        'type'      => 'group',
        'class'     => 'web',
        'children'  => array(
            'web-' . $web->id . '-url' => array(
                'type'      => 'textbox',
                'value'     => $web->url,
                'hint'      => Text::get('tooltip-user-webs'),
                'errors'    => array(),
                'required'  => true,
                'class'     => 'web-url inline'
            ),
            'web-' . $web->id . '-remove' => array(
                'type'      => 'submit',
                'label'     => Text::get('form-remove-button'),
                'class'     => 'web-remove inline remove'
            )
        )
    );

}
?>

<form method="post" action="/dashboard/profile/profile" class="project" enctype="multipart/form-data">

<?php echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('profile-main-header'),
    'hint'          => Text::get('guide-project-user-information'),
    'footer'        => array(
        'view-step-userPersonal' => array(
            'type'  => 'submit',
            'label' => Text::get('form-apply-button'),
            'name'  => 'save-userProfile',
            'class' => 'next'
        )
    ),
    'elements'      => array(
        'process_userProfile' => array (
            'type' => 'hidden',
            'value' => 'userProfile'
        ),
        'user_name' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => Text::get('profile-field-name'),
            'hint'      => Text::get('tooltip-user-name'),
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'value'     => $user->name,
        ),
        'user_location' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => Text::get('profile-field-location'),
            'hint'      => Text::get('tooltip-user-location'),
            'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
            'value'     => $user->location,
        ),
        'user_avatar' => array(
            'title'     => Text::get('profile-fields-image-title'),
            'type'      => 'group',
            'hint'      => Text::get('tooltip-user-image'),
            'errors'    => !empty($errors['avatar']) ? array($errors['avatar']) : array(),
            'class'     => 'user_avatar',
            'children'  => array(
                'avatar_upload'    => array(
                    'type'  => 'file',
                    'class' => 'inline avatar_upload',
                    'title' => Text::get('profile-field-avatar_upload'),
                    'hint'  => Text::get('tooltip-user-image'),
                ),
                'avatar-current' => array(
                    'type'  => 'group',
                    'title' => Text::get('profile-field-avatar_current'),
                    'class' => 'inline avatar',
                    'children'  => $image
                )

            )
        ),

        'user_about' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => Text::get('profile-field-about'),
            'hint'      => Text::get('tooltip-user-about'),
            'errors'    => !empty($errors['about']) ? array($errors['about']) : array(),
            'value'     => $user->about
        ),
        'interests' => array(
            'type'      => 'checkboxes',
            'name'      => 'user_interests[]',
            'title'     => Text::get('profile-field-interests'),
            'hint'      => Text::get('tooltip-user-interests'),
            'errors'    => !empty($errors['interests']) ? array($errors['interests']) : array(),
            'options'   => $interests
        ),
        'user_keywords' => array(
            'type'      => 'textbox',
            'size'      => 20,
            'title'     => Text::get('profile-field-keywords'),
            'hint'      => Text::get('tooltip-user-keywords'),
            'errors'    => !empty($errors['keywords']) ? array($errors['keywords']) : array(),
            'value'     => $user->keywords
        ),
        'user_contribution' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => Text::get('profile-field-contribution'),
            'hint'      => Text::get('tooltip-user-contribution'),
            'errors'    => !empty($errors['contribution']) ? array($errors['contribution']) : array(),
            'value'     => $user->contribution
        ),
        'user_webs' => array(
            'title'     => Text::get('profile-field-websites'),
            'hint'      => Text::get('tooltip-user-webs'),
            'class'     => 'webs',
            'children'  => $user_webs + array(
                'web-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add'
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
                    'value'     => $user->facebook
                ),
                'user_twitter' => array(
                    'type'      => 'textbox',
                    'class'     => 'twitter',
                    'size'      => 40,
                    'title'     => 'Twitter',
                    'hint'      => Text::get('tooltip-user-twitter'),
                    'errors'    => !empty($errors['twitter']) ? array($errors['twitter']) : array(),
                    'value'     => $user->twitter
                ),
                'user_identica' => array(
                    'type'      => 'textbox',
                    'class'     => 'identica',
                    'size'      => 40,
                    'title'     => Text::get('regular-identica'),
                    'hint'      => Text::get('tooltip-user-identica'),
                    'errors'    => !empty($errors['identica']) ? array($errors['identica']) : array(),
                    'ok'        => !empty($okays['identica']),
                    'value'     => $user->identica
                ),
                'user_linkedin' => array(
                    'type'      => 'textbox',
                    'class'     => 'linkedin',
                    'size'      => 40,
                    'title'     => 'LinkedIn',
                    'hint'      => Text::get('tooltip-user-linkedin'),
                    'errors'    => !empty($errors['linkedin']) ? array($errors['linkedin']) : array(),
                    'value'     => $user->linkedin
                )
            )
        )
    )
));

?>
</form>
