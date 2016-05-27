<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$user = $vars['user'];
$original = $vars['original'];
$errors = $vars['errors'];

$sfid = 'sf-project-profile';
?>

<form method="post" action="/dashboard/translates/profile/save" class="project" enctype="multipart/form-data">

<?php echo SuperForm::get(array(
    'id'            => $sfid,
    'action'        => '',
    'level'         => 3,
    'method'        => 'post',
    'title'         => '',
    'hint'          => Text::get('guide-project-user-information'),
    /*
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-userProfile',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    */
    'elements'      => array(
        'process_userProfile' => array (
            'type' => 'hidden',
            'value' => 'userProfile'
        ),
        'id' => array (
            'type' => 'hidden',
            'value' => $user->id
        ),
        'name-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('profile-field-name'),
            'html'     => nl2br($original->name)
        ),
        'name' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'class'     => 'inline',
            'title'     => '',
            'hint'      => Text::get('tooltip-user-name'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => $user->name
        ),
        'about-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('profile-field-about'),
            'html'     => nl2br($original->about)
        ),
        'about' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'class'     => 'inline',
            'title'     => '',
            'hint'      => Text::get('tooltip-user-about'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => $user->about
        ),
        'keywords-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('profile-field-keywords'),
            'html'     => $original->keywords
        ),
        'keywords' => array(
            'type'      => 'textbox',
            'size'      => 20,
            'class'     => 'inline',
            'title'     => '',
            'hint'      => Text::get('tooltip-user-keywords'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => $user->keywords
        ),
        'contribution-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('profile-field-contribution'),
            'html'     => nl2br($original->contribution)
        ),
        'contribution' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'class'     => 'inline',
            'title'     => '',
            'hint'      => Text::get('tooltip-user-contribution'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => $user->contribution
        )
    )
));
?>
</form>
