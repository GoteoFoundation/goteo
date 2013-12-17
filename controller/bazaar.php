<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Check,
        Goteo\Library\Text,
        Goteo\Library\Message,
        Goteo\Library\Paypal,
        Goteo\Library\Tpv;

    class Bazaar extends \Goteo\Core\Controller {
    
        public function index($id = null, $show = null) {

            $ogimages = array();

            $page = Page::get('bazar');

            list($page->txt1, $page->txt2, $page->txt3) = explode('<hr />', $page->content);

            $URL = (NODE_ID != GOTEO_NODE) ? NODE_URL : SITE_URL;
            $page->url = $URL.'/bazaar';
            $lsuf = (LANG != 'es') ? '?lang='.LANG : '';

            $page->debug = ($URL == 'http://beta.goteo.org' || $URL == 'http://devgoteo.org');

            $vpath = "view/bazar/";
            $vdata = array();

            $ogimages[] = $URL.'/view/bazar/img/carro.png';

            if ($id !== null) {
                $item=Model\Bazar::get($id);
                if (!$item instanceof Model\Bazar)
                    throw new Redirection("/bazaar"); 

                $item->imgsrc = (!empty($item->img)) ? '/data/images/'.$item->img->name : '/data/images/bazaritem.svg';
                $ogimages[] = $URL.$item->imgsrc;
                $vdata["item"] = $item;

                // página interna
                $page->home = false;

                // veamos si puede usar paypal
                $item->project->called = Model\Call\Project::miniCalled($item->project->id);
                if ($item->project->called) {
                    $item->project->allowpp = false;
                } else {
                    $item->project->allowpp = Model\Project\Account::getAllowpp($item->project->id);
                }

                $ogurl = $URL.'/bazaar'.$id;
                // si el $show es de agradecimiento mostramos thanks.html.php
                if ($show == 'thanks') {
                    $vpath .= "thanks.html.php";
                } else {
                    $vpath .= "page.html.php";
                    // si el $show es de fail datos precargados normalmente desde $_SESSION
                }
             }
             else {
                // portada
                $page->home = true;

                $items = Model\Bazar::getAll();
                foreach ($items as &$item) {
                    $item->imgsrc = (!empty($item->img)) ? '/data/images/'.$item->img->name : '/data/images/bazaritem.svg';
                    $ogimages[] = $URL.$item->imgsrc;
                }
                $vdata["items"] = $items;
                $ogurl = $URL.'/bazaar';
                $vpath .= "home.html.php";
            }


            // enlaces de compartir
            $bazar_title = Text::get('bazar-spread-text', $page->name);
            $item_title = !empty($item->title) ? $item->title : $page->name;
            $item_title = Text::get('bazar-spread-text', $item_title);
            $item_description = !empty($item->description) ? $item->description : $page->description;
            $bazar_url = $page->url.$lsuf;
            $item_url = !empty($item->id) ? $page->url.'/'.$item->id.$lsuf : $bazar_url;
            $item_image = !empty($item->imgsrc) ? $URL.$item->imgsrc : $URL.'/view/bazar/img/carro.png';

            $vdata["share"] = (object) array(
                'description'=>$page->description, 
                'bazar_url'=>$bazar_url, 
                'bazar_twitter_url'=>'http://twitter.com/home?status=' . urlencode($bazar_title . ': ' . $bazar_url), 
                'bazar_facebook_url'=>'http://facebook.com/sharer.php?u=' . urlencode($bazar_url) . '&t=' . urlencode($bazar_title), 
                'item_url'=>$item_url, 
                'item_twitter_url'=>'http://twitter.com/home?status=' . urlencode($item_title . ': ' . $item_url), 
                'item_facebook_url'=>'http://facebook.com/sharer.php?u=' . urlencode($item_url) . '&t=' . urlencode($item_title), 
            );

            $vdata['page'] = $page;

            $vdata['ogmeta'] = array(
                'title' => $item_title,
                'description' => $item_description,
                'url' => $ogurl,
                'image' => $ogimages
            );

            return new View($vpath, $vdata);

        }

        /*
        * Metodo para procesar el aporte
        * es como controller/invest::index pero hace varias cosas de modo diferente
        * 
        */
        public function pay($reward = null) {
            if (empty($reward))
                throw new Redirection('/bazaar');

            // sacamos los datos del producto
            $item = Model\Bazar::get($reward);
            $projectData = Model\Project::get($item->project->id);

            // si no es un producto de bazar tenemos un problema

            $message = '';

            // metodos habilitados
            $methods = \Goteo\Controller\Invest::$methods;

            // si no está en campaña no pueden esta qui ni de coña, que elijan otro
            if ($projectData->status != 3) {
                Message::Info(Text::get('project-not_published'));
                throw new Redirection('/bazaar');
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {


                // hago un array de datos
                $_SESSION['bazar-form-data'] = $formData = array(
                    // el usuario
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],

                    // destinatario de regalo
                    'regalo' => $_POST['regalo'],
                    'namedest' => $_POST['namedest'],
                    'emaildest' => $_POST['emaildest'],

                    // dirección de destino
                    'address'  => $_POST['address'],
                    'zipcode'  => $_POST['zipcode'],
                    'location' => $_POST['location'],
                    'country'  => $_POST['country'],

                    // mensaje
                    'message'  => $_POST['message']
                );
                $_SESSION['bazar-form-data']['anonymous'] = $_POST['anonymous'];

                // si el usuario está logueado, usamos los datos de $_SESSION['user']
                // si no creamos un usuario de modo instantaneo
                if (isset($_SESSION['user']) && $_SESSION['user']->id == $_POST['user']) {
                    $formData['user'] = $_SESSION['user']->id;
                } elseif (!empty($formData['email']) && Check::mail($formData['email'])) {

                    $nUser = \Goteo\Controller\User::instantReg($formData['email'], $formData['name']);
                    if (!$nUser) {
                        Message::Error(Text::get('regular-login'));
                        throw new Redirection("/user/login?return=".urlencode('/bazaar/'.$item->id));
                    }
                    $formData['user'] = $nUser;

                } else {
                    Message::Error(Text::get('register-confirm_mail-fail', \GOTEO_MAIL));
                    throw new Redirection("/bazaar/{$reward}/fail");
                }

                $errors = array();
                $los_datos = $_POST;
                $method = \strtolower($_POST['method']);

                // si nos están usando un método no habilitado
                if (!isset($methods[$method])) {
                    Message::Error(Text::get('invest-method-error'));
                    throw new Redirection("/bazaar/{$reward}/fail");
                }

                // dirección de envio 
                $address = $formData;


                // verificación de impulsor 
                if ($project->owner == $formData['user']) {
                    Message::Error(Text::get('invest-owner-error'));
                    throw new Redirection("/bazaar/{$item->id}/fail");
                }

                $invest = new Model\Invest(
                    array(
                        'amount' => $item->amount,
                        'user' => $formData['user'],
                        'project' => $projectData->id,
                        'method' => $method,
                        'status' => '-1',               // aporte en proceso
                        'invested' => date('Y-m-d'),
                        'anonymous' => $_POST['anonymous'],
                        'resign' => false,
                        'url' => "/bazar/{$item->id}/" // url de redirección
                    )
                );

                // recompensa
                $invest->rewards = array($item->reward);

                $invest->address = (object) $address;

                // también puede ser que genere capital riego...
                // saber si el aporte puede generar riego y cuanto
                if ($projectData->called->dropable) {

                    // saber si este usuario ya ha generado riego
                    $allready = $projectData->called->getSupporters(true, $formData['user'], $projectData->id);
                    if ($allready > 0) {
                        $invest->called = null;
                    } else  {
                        $invest->called = $projectData->called;
                        $invest->maxdrop = Model\Call\Project::currMaxdrop($projectData, $invest->amount);
                    }
                } else {
                    $invest->called = null;
                }

                if ($invest->save($errors)) {
                    $invest->urlOK  = "/invest/confirmed/bazargoteo/{$invest->id}/{$item->id}";
                    $invest->urlNOK = "/invest/fail/bazargoteo/{$invest->id}/{$item->id}";
                    Model\Invest::setDetail($invest->id, 'init', 'Se ha creado el registro de aporte, el usuario ha clickado el boton de tpv o paypal. Proceso controller/invest');

                    switch($method) {
                        case 'tpv':
                            // redireccion al tpv
                            if (Tpv::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::Error(Text::get('invest-tpv-error_fatal'));
                            }
                            break;
                        case 'paypal':
                            // Petición de preapproval y redirección a paypal
                            if (Paypal::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::Error(Text::get('invest-paypal-error_fatal'));
                            }
                            break;
                        case 'cash':
                            $invest->setStatus('1');
                            // En betatest aceptamos cash para pruebas
                            throw new Redirection($invest->urlOK);
                            break;
                    }
                } else {
                    Message::Error(Text::get('invest-create-error'));
                }

            } else {
                Message::Error(Text::get('invest-data-error'));
            }

            throw new Redirection("/bazaar/{$reward}/fail");
        }

    }
		
}
