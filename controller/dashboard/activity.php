<?php

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\Redirection,
		Goteo\Library\Message,
        Goteo\Library\File,
        Goteo\Library\Text,
		Goteo\Library\Check,
        Goteo\Library\Listing;

    class Activity {

        // listados de proyectos a mostrar (proyectos que cofinancia y proyectos suyos)
        public static function projList ($user) {
            $lists = array();
            // mis proyectos
            $projects = Model\Project::ofmine($user->id);
            if (!empty($projects)) {
                $lists['my_projects'] = Listing::get($projects);
            }
            // proyectos que cofinancio
            $invested = Model\User::invested($user->id, false);
            if (!empty($invested)) {
                $lists['invest_on'] = Listing::get($invested);
            }
            return $lists;
        }
        
        
        // eventos a mostrar en su muro
        public static function wall ($user) {
            return null;
            
            /*
             * Depurar antes de poner esto
             *
              // eventos privados del usuario
              $items['private'] = Feed::getUserItems($_SESSION['user']->id, 'private');
              // eventos de proyectos que he cofinanciado
              $items['supported'] = Feed::getUserItems($_SESSION['user']->id, 'supported');
              // eventos de proyectos donde he mensajeado o comentado
              $items['comented'] = Feed::getUserItems($_SESSION['user']->id, 'comented');
             *
             */
        }

        // acciones de certificado de donativo
        public static function donor ($user, $action = 'view') {

            $errors = array();

            $year = date('Y');
            $month = date('m');
            $day = date('d');
            // hasta junio es el año anterior
            if ($month <= 6) {
                $year--;
            }

            // ver si es donante, cargando sus datos
            $donation = Model\User\Donor::get($user->id, $year);
            $donation->dates = Model\User\Donor::getDates($donation->user, $donation->year);
            $donation->userData = Model\User::getMini($donation->user);

            if (!$donation || !$donation instanceof Model\User\Donor) {
                Message::Error(Text::get('dashboard-donor-no_donor', $year));
                throw new Redirection('/dashboard/activity');
            }

            // no permitir confirmar a partir del 10 de enero
            if ($year != date('Y')
                && ( ($month == 1 && $day > 15) || $month > 1 )
            ) {
                $donation->confirmable = false;
                if ($action == 'confirm') {
                    Message::Error(Text::get('dashboard-donor-confirm_closed', $year));
                    throw new Redirection('/dashboard/activity');
                }
            } else {
                $donation->confirmable = true;
            }

            $donation->amount = 0;
            foreach ($donation->dates as $inv) {
                $donation->amount += $inv->amount;
            }



            if ($action == 'edit' && $donation->confirmed) {
                Message::Error(Text::get('dashboard-donor-confirmed'));
                throw new Redirection('/dashboard/activity/donor');
            }

            // si están guardando, actualizar los datos y guardar
            if ($action == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['save'] == 'donation') {

                $donation->edited = 1;
                $donation->confirmed = 0;
                $donation->name = $_POST['name'];
                $donation->surname = $_POST['surname'];
                $donation->nif = $_POST['nif'];
                $donation->address = $_POST['address'];
                $donation->zipcode = $_POST['zipcode'];
                $donation->location = $_POST['location'];
                $donation->country = $_POST['country'];
                $donation->year = $year;

                if ($donation->save($errors)) {
                    Message::Info(Text::get('dashboard-donor-saved'));
                    throw new Redirection('/dashboard/activity/donor');
                } else {
                    Message::Error(implode('<br />', $errors));
                    Message::Error(Text::get('dashboard-donor-save_fail'));
                    throw new Redirection('/dashboard/activity/donor/edit');
                }
            }

            if ($action == 'confirm') {

                $ok = true;

                // verificar que han rellenado todos los campos
                if (empty($donation->name)
                    || empty($donation->surname)
                    || empty($donation->nif)
                    || empty($donation->address)
                    || empty($donation->zipcode)
                    || empty($donation->location)
                    || empty($donation->country)
                ) {
                    $ok = false;
                    Message::Error(Text::get('validate-donor-mandatory'));
                }
                // nombre
                // apellidos
                // nif
                // address
                // zipcode
                // location
                // country

                // verificar que el nif es correcto
                if (!Check::nif($donation->nif)) {
                    Message::Error(Text::get('validate-project-value-contract_nif'));
                    $ok = false;
                }

                if ($ok) {
                    // marcamos que los datos estan confirmados
                    if (Model\User\Donor::setConfirmed($user->id, $year)) {
                        Message::Info(Text::get('dashboard-donor-confirmed'));
                    }
                }

                throw new Redirection('/dashboard/activity/donor');
            }

            if ($action == 'download') {

                if (!$donation->confirmed) {
                    Message::Error(Text::get('dashboard-donor-pdf_closed', $year));
                    throw new Redirection('/dashboard/activity/donor');
                }

                // verificar que el nif es correcto
                if (!Check::nif($donation->nif)) {
                    Message::Error(Text::get('validate-project-value-contract_nif'));
                    throw new Redirection('/dashboard/activity/donor');
                }

                if (empty($donation->name)
                    || empty($donation->surname)
                    || empty($donation->nif)
                    || empty($donation->address)
                    || empty($donation->zipcode)
                    || empty($donation->location)
                    || empty($donation->country)
                ) {
                    Message::Error(Text::get('validate-donor-mandatory'));
                    throw new Redirection('/dashboard/activity/donor');
                }

                // borramos el pdf anterior y generamos de nuevo
                if (!empty($donation->pdf)) {
                    $fp = new File();
                    $fp->setBucket(AWS_S3_BUCKET_DOCUMENT, 'certs/');

                    if ($fp->exists($donation->pdf)) {
                        $fp->delete($donation->pdf);
                    }
                }

                // para generar: 
                // preparamos los datos para el pdf
                // generamos el pdf y lo mosteramos con la vista específica
                // estos pdf se guardan en el bucket de documentos /certs
                // el formato del archivo es: Ymd_nif_userid

                $objeto = new \Goteo\Library\Num2char($donation->amount, null);
                $donation->amount_char = $objeto->getLetra();

                $fp = new File();
                $dir = 'pdfs/donativos/';
                $filename = "cer{$donation->year}_" . date('Ymd') . "_{$donation->nif}_{$donation->user}.pdf";

                $debug = false;

                if ($debug)
                    header('Content-type: text/html');

                require_once 'library/pdf.php';  // Libreria pdf
                $pdf = donativeCert($donation);

                if ($debug) {
                    echo 'FIN';
                    echo '<hr><pre>' . print_r($pdf, true) . '</pre>';
                } else {
                        //guardar pdf en temporal y luego subir a remoto (s3 o data/ si es local)
                        $tmp = tempnam(sys_get_temp_dir(), 'goteo-img');
                        $pdf->Output($tmp, 'F');
                        //guardamos a remoto (acceso privado)
                        if($fp->upload($tmp, $dir . $filename, 'bucket-owner-full-control')) {
                            // si se graba lo ponemos en el registro para que a la próxima se cargue
                            $donation->setPdf($filename);
                        }
                        unlink($tmp);

                }

                header('Content-type: application/pdf');
                // y forzamos la descarga (desde static.goteo.org)
                header("Content-disposition: attachment; filename={$donation->pdf}");
                header("Content-Transfer-Encoding: binary");
                echo $fp->get_contents($dir . $filename);
                die;
            }
            // fin action download


            return $donation;
            
        }

    }

}
