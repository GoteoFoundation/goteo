<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

$user = $this['user'];
$errors = $this['errors'];
$this['level'] = 3;

$message = $this['action'] == 'recover' ? $this['message'] : '';

extract($_POST);
?>
<form action="/dashboard/profile/access" method="post" enctype="multipart/form-data">

<?php
echo new SuperForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'hint'          => Text::get('guide-dashboard-user-access'),
    'elements'      => array(

        'action' => array(
            'type' => 'hidden',
            'value' => $this['action']
        ),

        'data' => array(
            'type'  => 'html',
            'title' => 'Datos de acceso',
            'hint'  => Text::get('tooltip-dashboard-user-access_data'),
            'html'  => '<strong>Login: </strong>'.$user->id.'&nbsp;&nbsp;&nbsp;<strong>E-mail: </strong>'.$user->email
        ),

        'change_email' => array(
            'type'      => 'group',
            'title'     => 'Cambiar email',
            'hint'      => Text::get('tooltip-dashboard-user-change_email'),
            'children'  => array(
                'user_nemail' => array(
                    'type'  => 'textbox',
                    'class' => 'inline',
                    'title' => 'Nuevo email',
                    'hint'  => Text::get('tooltip-dashboard-user-new_email'),
                    'errors'=> !empty($errors['email']['new']) ? array($errors['email']['new']) : array(),
                    'value' => $user_nemail
                ),
                'user_remail' => array(
                    'type'  => 'textbox',
                    'class' => 'inline',
                    'title' => 'Confirmar nuevo E-mail',
                    'hint'  => Text::get('tooltip-dashboard-user-confirm_email'),
                    'errors'=> !empty($errors['email']['retry']) ? array($errors['email']['retry']) : array(),
                    'value' => $user_remail
                ),
                'change_email' => array(
                    'type'      => 'submit',
                    'label'     => 'Cambiar E-mail',
                    'class'     => 'save'
                )

            )
        ),

        'change_password' => array(
            'type'      => 'group',
            'title'     => 'Cambiar contraseña',
            'hint'      => Text::get('tooltip-dashboard-user-change_password'),
            'children'  => array(
                'user_password' => array(
                    'type'  => 'password',
                    'class' => 'inline',
                    'title' => 'Contraseña actual',
                    'hint'  => Text::get('tooltip-dashboard-user-user_password'),
                    'errors'=> !empty($errors['password']) ? array($errors['password']) : array(),
                    'value' => $user_password
                ),
                'pass_anchor' => array(
                    'type'  => 'html',
                    'html'  => '<a name="password"></a>' . $messge
                ),
                'user_npassword' => array(
                    'type'  => 'password',
                    'class' => 'inline',
                    'title' => 'Nueva Contraseña',
                    'hint'  => Text::get('tooltip-dashboard-user-new_password'),
                    'errors'=> !empty($errors['password']['new']) ? array($errors['password']['new']) : array(),
                    'value' => $user_npassword
                ),
                'user_rpassword' => array(
                    'type'  => 'password',
                    'class' => 'inline',
                    'title' => 'Confirmar nueva contraseña',
                    'hint'  => Text::get('tooltip-dashboard-user-confirm_password'),
                    'errors'=> !empty($errors['password']['retry']) ? array($errors['password']['retry']) : array(),
                    'value' => $user_rpassword
                ),
                'change_password' => array(
                    'type'      => 'submit',
                    'label'     => 'Cambiar contraseña',
                    'class'     => 'save'
                )

            )
        ),




    )

));

?>

</form>
<hr />
<a class="button red" href="<?php echo SITE_URL ?>/user/leave?email=<?php echo $user->email ?>"><?php echo Text::get('login-leave-header'); ?></a>