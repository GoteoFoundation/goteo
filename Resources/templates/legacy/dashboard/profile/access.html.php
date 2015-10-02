<?php

use Goteo\Library\Text,
    Goteo\Library\NormalForm;

$user = $vars['user'];
$errors = $vars['errors'];
$vars['level'] = 3;

$message = $vars['action'] == 'recover' ? $vars['message'] : '';

extract($_POST);
?>
<form action="<?php echo SEC_URL; ?>/dashboard/profile/access" method="post" enctype="multipart/form-data">

<?php
echo new NormalForm(array(

    'level'         => $vars['level'],
    'method'        => 'post',
    'hint'          => Text::get('guide-dashboard-user-access'),
    'elements'      => array(

        'action' => array(
            'type' => 'hidden',
            'value' => $vars['action']
        ),

        'data' => array(
            'type'  => 'HTML',
            'title' => Text::get('dashboard-menu-profile-access'),
            'hint'  => Text::get('tooltip-dashboard-user-access_data'),
            'html'  => '<strong>'.Text::get('login-access-username-field').': </strong>'.$user->id.'<br /><strong>'.Text::get('login-register-email-field').': </strong>'.$user->email
        ),

        'change_email' => array(
            'type'      => 'group',
            'title'     => Text::get('user-changeemail-title'),
            'hint'      => Text::get('tooltip-dashboard-user-change_email'),
            'children'  => array(
                'user_nemail' => array(
                    'type'  => 'TextBox',
                    'class' => 'inline',
                    'title' => Text::get('login-register-email-field'),
                    'hint'  => Text::get('tooltip-dashboard-user-new_email'),
                    'errors'=> !empty($errors['email']) ? array($errors['email']) : array(),
                    'value' => $user_nemail
                ),
                'user_remail' => array(
                    'type'  => 'TextBox',
                    'class' => 'inline',
                    'title' => Text::get('login-register-confirm-field'),
                    'hint'  => Text::get('tooltip-dashboard-user-confirm_email'),
                    'errors'=> !empty($errors['email_retry']) ? array($errors['email_retry']) : array(),
                    'value' => $user_remail
                ),
                'change_email' => array(
                    'type'      => 'submit',
                    'label'     => Text::get('form-apply-button'),
                    'class'     => 'save'
                )

            )
        ),

        'change_password' => array(
            'type'      => 'group',
            'title'     => Text::get('user-changepass-title'),
            'hint'      => Text::get('tooltip-dashboard-user-change_password'),
            'children'  => array(
                /* Ya no requerimos la pasword actual.
                'user_password' => $old_pass ,
                 */
                'pass_anchor' => array(
                    'type'  => 'HTML',
                    'class' => 'inline',
                    'html'  => '<a name="password"></a>' . $messge
                ),
                'user_npassword' => array(
                    'type'  => 'Password',
                    'class' => 'inline',
                    'title' => Text::get('user-changepass-new'),
                    'hint'  => Text::get('tooltip-dashboard-user-new_password'),
                    'errors'=> !empty($errors['password_new']) ? array($errors['password_new']) : array(),
                    'value' => $user_npassword
                ),
                'user_rpassword' => array(
                    'type'  => 'Password',
                    'class' => 'inline',
                    'title' => Text::get('user-changepass-confirm'),
                    'hint'  => Text::get('tooltip-dashboard-user-confirm_password'),
                    'errors'=> !empty($errors['password_retry']) ? array($errors['password_retry']) : array(),
                    'value' => $user_rpassword
                ),
                'change_password' => array(
                    'type'      => 'Submit',
                    'label'     => Text::get('form-apply-button'),
                    'class'     => 'save'
                )

            )
        ),




    )

));

?>

</form>
<hr />
<a class="button red" href="<?php echo SEC_URL ?>/user/leave?email=<?php echo $user->email ?>"><?php echo Text::get('login-leave-header'); ?></a>
