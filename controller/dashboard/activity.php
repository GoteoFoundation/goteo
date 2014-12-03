<?php

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\Redirection,
		Goteo\Library\Message,
        Goteo\Library\FileHandler\File,
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

            $unconfirmable = false;
            $year = Model\User\Donor::currYear($unconfirmable);

            // ver si es donante ;  echo \trace($user);

            // el método get si solo hay un aporte a un proyecto no financiado devolverá vacio
            $donation = Model\User\Donor::get($user->id, $year);

            if (!isset($donation) || !$donation instanceof \Goteo\Model\User\Donor) {
                // hacemos que no pueda confirmar pero que pueda poner los datos,
                //  así verá en el listado de fechas que hay aportes a proyectos pendientes
                $donation = new \Goteo\Model\User\Donor();
                $donation->user = $user->id;
                $donation->year = $year; //para obtener las fechas de aportes (si los hay)
                $donation->confirmable = false; // si permitimos editar/confirmar se crea registro en user_donor emitiendo un certificado falso
                $donation->confirmed = false; // para que no pueda descargar de ningún modo
            }

            // getDates da todos los aportes, incluso a proyectos aun no financiados
            $donation->dates = Model\User\Donor::getDates($donation->user, $donation->year, false);

            // claro que si no tiene ningún aporte si que lo sacamos de esta página
            if (empty($donation->dates)) {
                // tendrá el message de  'dashboard-donor-no_donor' anterior
                throw new Redirection('/dashboard/activity');
            }

            $donation->amount = 0; // para certificado
            foreach ($donation->dates as $inv) {

                // si un solo aporte pendiente no podrán confirmar
                if (!$inv->funded || $inv->issue || $inv->preapproval)
                    $donation->confirmable = false;
                else
                    $donation->amount += $inv->amount;
            }

            // no permitir confirmar a partir del 10 de enero
            if ($donation->confirmable === false || $unconfirmable) {

                // aviso que el certificado aun no está disponible
                Message::Error(Text::get('dashboard-donor-no_donor', $year));

                $donation->confirmable = false;
                if ($action == 'confirm') {
                    Message::Error(Text::get('dashboard-donor-confirm_closed', $year));
                    // aquí si que lo sacamos, no permitimos confirmar
                    throw new Redirection('/dashboard/activity/donor');
                }
            } elseif (isset($donation) && $donation instanceof Model\User\Donor && $donation->edited && !$donation->confirmed) {
                // si ha editado pero no ha confirmado
                Message::Info(Text::get('dashboard-donor-remember'));
            }

            if ($action == 'edit' && $donation->confirmed) {
                Message::Error(Text::get('dashboard-donor-confirmed', $donation->year));
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
                $donation->location = $_POST['location'];
                $donation->zipcode = $_POST['zipcode'];
                $donation->region = $_POST['region'];
                $donation->country = $_POST['country'];
                $donation->countryname = ($donation->country == 'other') ? $_POST['countryname'] : '';
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

            if ($donation->edited || $action == 'confirm' || $action == 'download') {

                // ver si es un cif
                $donation->nif_type = '';
                $donation->valid_nif = Check::nif($donation->nif, $donation->nif_type);
                $donation->juridica = ($donation->nif_type == 'cif' || $donation->nif_type == 'vat');

                // verificar que han rellenado todos los campos
                if (empty($donation->name)
                    || ( !$donation->juridica && empty($donation->surname) )
                    || empty($donation->nif)
                    || empty($donation->address)
                    || empty($donation->zipcode)
                    || empty($donation->location) // ciudad
                    || empty($donation->region)  // provincia
                    || empty($donation->country)
                ) {
                    Message::Error(Text::get('validate-donor-mandatory'));
                    Model\User\Donor::setConfirmed($user->id, $year, 0);
                    $donation->edited = false;
                    $donation->confirmed = false;
                    $donation->confirmable = false;
                }
                // nombre
                // apellidos
                // nif
                // address
                // zipcode
                // location = ciudad
                // region = provincia
                // country

                // verificar que el nif es correcto
                if ($donation->valid_nif === false) {
                    Message::Error(Text::get('validate-project-value-contract_nif'));
                    Model\User\Donor::setConfirmed($user->id, $year, 0);
                    $donation->edited = false;
                    $donation->confirmed = false;
                    $donation->confirmable = false;
                }

                if ($donation->confirmable !== false && $action == 'confirm') {
                    // marcamos que los datos estan confirmados
                    if (Model\User\Donor::setConfirmed($user->id, $year)) {
                        Message::Info(Text::get('dashboard-donor-confirmed', $year));
                    }
                    throw new Redirection('/dashboard/activity/donor');

                } elseif ($action == 'download' && !$donation->confirmed) {
                    Message::Error(Text::get('dashboard-donor-pdf_closed', $year));

                } elseif ($action == 'download' && $donation->confirmed) {
                    // para generar:
                    // preparamos los datos para el pdf
                    // generamos el pdf y lo mosteramos con la vista específica
                    // estos pdf se guardan en el bucket de documentos /certs
                    // el formato del archivo es: Ymd_nif_userid

                    $objeto = new \Goteo\Library\Num2char($donation->amount, null);
                    $donation->amount_char = $objeto->getLetra();


                    $filename = "cer{$donation->year}_" . date('Ymd') . "_{$donation->nif}_{$donation->user}.pdf";
                    // actualizamos el nombre de archivo descargado
                    $donation->setPdf($filename);

                    $debug = false;

                    // más datos para certificado
                    $donation->userData = Model\User::getMini($donation->user);
                    $donation->dates = Model\User\Donor::getDates($donation->user, $donation->year); // solo financiados

                    require_once 'library/pdf.php';  // Libreria pdf
                    $pdf = donativeCert($donation);

                    if ($debug) {
                        header('Content-type: text/html');
                        echo 'FIN';
                        echo '<hr><pre>' . print_r($pdf, true) . '</pre>';
                    }

                    // y se lo damos para descargar
                    echo $pdf->Output($filename, 'D');

                    die;

                }

            }

            return $donation;

        }

    }

}
