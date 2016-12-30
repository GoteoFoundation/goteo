<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Gregwar\Captcha\CaptchaBuilder;

use Goteo\Library;
use Goteo\Library\Text;
use Goteo\Model\Page;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Model\Template;
use Goteo\Model\Mail;

class ContactController extends \Goteo\Core\Controller {

    public function indexAction (Request $request) {

        $tags=[ 'contact-form-user-tag-name'         => Text::get('contact-form-user-tag-description'),
                'contact-form-new-project-tag-name'  => Text::get('contact-form-new-project-tag-description'),
                'contact-form-project-form-tag-name' => Text::get('contact-form-project-form-tag-description'),
                'contact-form-dev-tag-name' => Text::get('contact-form-dev-tag-description'),
                'contact-form-relief-tag-name' => Text::get('contact-form-relief-tag-description'),
                'contact-form-service-tag-name' => Text::get('contact-form-service-tag-description'),
                'contact-form-others-tag-name' => Text::get('contact-form-others-tag-description'),

        ];

        $errors = array();
        $data = [];
        if($user = Session::getUser()) {
            $data['name'] = $user->name;
            $data['email'] = $user->email;
        }
        if ($request->isMethod('POST')) {

            $name = $request->request->get('name');
            $tag = $request->request->get('tag');
            $email = $request->request->get('email');
            $subject = $request->request->get('subject');
            $message = $request->request->get('message');

            // si falta mensaje, email o asunto, error
            if(empty($email)) {
                $errors['email'] = Text::get('error-contact-email-empty');
            } elseif(!Library\Check::mail($email)) {
                $errors['email'] = Text::get('error-contact-email-invalid');
            }

            if(empty($subject)) {
                $errors['subject'] = Text::get('error-contact-subject-empty');
            }

            if(empty($message)) {
                $errors['message'] = Text::get('error-contact-message-empty');
            } else {
                $msg_content = nl2br(strip_tags($message));
            }

            // check captcha
            if(!$user && $phrase = Session::get('captcha-phrase')) {
                Session::del('captcha-phrase');
                $captcha = new CaptchaBuilder($phrase);
                // captcha verification
                if (!$captcha->testPhrase($request->request->get('captcha_response'))) {
                    $errors['recaptcha'] = Text::get('error-contact-captcha');
                }
            }

            // check from token (ensures is a submit from the navigator)
            if($token = Session::get('form-token')) {
                Session::del('form-token');
                if($token !== $request->request->get('form-token')) {
                    $errors['form-token'] = 'Error submiting the form. Please try again!';
                }
            }

            $data = array(
                    'tag' => $tag,
                    'subject' => $subject,
                    'name'    => $name,
                    'email'   => $email,
                    'message' => $message
            );

            if (empty($errors)) {

                switch ($tag) {
                    //Acount problems
                    case 'contact-form-user-tag-name':
                        $to_admin = Config::get('mail.contact');
                        $user_template=Template::CONTACT_AUTO_REPLY_ACCOUNT_PROBLEMS;
                        break;
                    // New project chance
                    case 'contact-form-new-project-tag-name':
                        $to_admin = Config::get('mail.contact');
                        $user_template=Template::CONTACT_AUTO_REPLY_NEW_PROJECT;
                        break;
                    // Queries about the project form
                    case 'contact-form-project-form-tag-name':
                         $to_admin = Config::get('mail.contact');
                         $user_template=Template::CONTACT_AUTO_REPLY_PROJECT_FORM;
                        break;
                    // Dev
                    case 'contact-form-dev-tag-name':
                         $to_admin = Config::get('mail.fail');
                         $user_template=Template::CONTACT_AUTO_REPLY_DEV;
                        break;
                    // Relief
                    case 'contact-form-relief-tag-name':
                        $to_admin = Config::get('mail.donor');
                        $user_template=Template::CONTACT_AUTO_REPLY_RELIEF;
                        break;
                    // Service
                    case 'contact-form-service-tag-name':
                         $to_admin = Config::get('mail.management');
                        break;
                    //Others
                    default:
                        $to_admin = Config::get('mail.contact');
                        break;
                }

                if($user_template)
                {
                    //Sent an automatic mail to the user depending on the tag
                    $to_user=$email;
                    $toName = Config::get('mail.contact_name');
                    if(empty($toName)) $toName = 'Goteo';
                    // Obtenemos la plantilla para asunto y contenido
                    $mailHandler = Mail::createFromTemplate($to_user, $toName, $user_template);

                    $mailHandler->replyName = Config::get('transport.name');
                    $mailHandler->reply = Config::get('transport.from');

                    if (!$mailHandler->send($errors))
                        Message::error('Ha fallado al enviar el mensaje.');
                }


                //Sent mail to manage the contact
                $toName = Config::get('mail.contact_name');
                if(empty($toName)) $toName = 'Goteo';
                // Obtenemos la plantilla para asunto y contenido
                $mailHandler = Mail::createFromTemplate($to_admin, $toName, Template::MESSAGE_CONTACT, [
                        '%TONAME%'     => $toName,
                        '%MESSAGE%'    => $msg_content,
                        '%USEREMAIL%'  => $name . ' ' . $email
                    ]);
                // Custom subject
                $subject = ($tag ? '[' . Text::get($tag) . '] ' : '') . $subject;

                $mailHandler->subject = $subject;

                $mailHandler->replyName = $name;
                $mailHandler->reply = $email;

                if ($mailHandler->send($errors)) {
                    Message::info('Mensaje de contacto enviado correctamente.');
                    return $this->redirect('/contact');
                } else {
                    Message::error('Ha fallado al enviar el mensaje.');
                }
            }
        }

        $captcha = null;
        // Generate a new captcha on non-logged users
        if(!Session::isLogged()) {
            $captcha = new CaptchaBuilder();
            $captcha->build();
            Session::store('captcha-phrase', $captcha->getPhrase());
        }
        // Generate a new form token
        $token = sha1(uniqid(mt_rand(), true));
        Session::store('form-token', $token);

        return $this->viewResponse('about/contact',
            array(
                'data'    => $data,
                'tags'    => $tags,
                'token'    => $token,
                'page'    => Page::get('contact'),
                'captcha' => $captcha,
                'errors'  => $errors
            )
        );

    }

    // Creates a new image
    public function captchaAction() {
        $captcha = new CaptchaBuilder();
        $captcha->build();
        Session::store('captcha-phrase', $captcha->getPhrase());
        return $this->rawResponse($captcha->inline());
    }

}

