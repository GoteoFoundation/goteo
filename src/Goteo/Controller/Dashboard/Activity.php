<?php

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\Redirection,
		Goteo\Library\Message,
        Goteo\Library\FileHandler\File,
        Goteo\Library\Text,
		Goteo\Library\Check,
        Goteo\Library\Listing,
        Goteo\Library\PDF;

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

        // gestion de gotas
        public static function pool ($user, $action) {

            // ver si tiene reserva de gotas
            $pool = Model\User\Pool::get($user);

            // si tiene gotas, buscar recomendaciones según este último proyecto
            if ($pool->amount > 0) {

                // ver cual es el último proyecto (en el que se usaron gotas o el que guardó las primeras gotas)
                $pool->project = Model\User\Pool::lastProject($user);

                // por categoria

                // por localización

                $pool->recomended = array();

            }

            return $pool;

        }

        // acciones de certificado de donativo
        public static function donor ($user, $action = 'view') {
            $errors = array();

            $confirm_closed = false;
            $year = Model\User\Donor::currYear($confirm_closed);

            // ver si es donante ;  echo \trace($user);

            // el método get si solo hay un aporte a un proyecto no financiado devolverá vacio
            $donation = Model\User\Donor::get($user->id, $year);

            if (!isset($donation) || !$donation instanceof \Goteo\Model\User\Donor) {
                // hacemos que no pueda confirmar pero que pueda poner los datos,
                //  así verá en el listado de fechas que hay aportes a proyectos pendientes
                $donation = new \Goteo\Model\User\Donor();
                $donation->user = $user->id;
                $donation->year = $year; //para obtener las fechas de aportes (si los hay)
                $donation->confirmable = false; // si permitieramos editar/confirmar se crearia registro en user_donor emitiendo un certificado falso
                $donation->confirmed = false; // para que no pueda descargar de ningún modo
            }

            if ($confirm_closed) $donation->confirmable = false;

            // getDates da todos los aportes, incluso a proyectos aun no financiados
            $donation->dates = Model\User\Donor::getDates($donation->user, $donation->year, false);

            // claro que si no tiene ningún aporte si que lo sacamos de esta página
            if (empty($donation->dates)) {
                // tendrá el message de  'dashboard-donor-no_donor' anterior
                Message::Error(Text::get('dashboard-donor-no_donor', $year));
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

            // no permitir confirmar datos a partir del 10 de enero
            if ($action == 'confirm' && !$donation->confirmable ) {

                if ($confirm_closed) {
                    // aviso que el certificado aun no está disponible
                    Message::Error(Text::get('dashboard-donor-confirm_closed', $year));
                    throw new Redirection('/dashboard/activity/donor');
                }

            } elseif (isset($donation) && $donation instanceof Model\User\Donor && $donation->edited && !$donation->confirmed && !$confirm_closed) {
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

            if ($donation->edited || $action == 'confirm') {

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
                    $donation->edited = false;
                    $donation->confirmable = false;
                    Message::Error(Text::get('validate-donor-mandatory'));
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
                    $donation->edited = false;
                    $donation->confirmable = false;
                }


                if ($donation->confirmable !== false && $action == 'confirm') {
                    // marcamos que los datos estan confirmados
                    if (Model\User\Donor::setConfirmed($user->id, $year)) {
                        $donation->confirmed = true;
                        $action = 'download';
                    } else {
                        throw new Redirection('/dashboard/activity/donor');
                    }
                }

            }

            if ($action == 'download') {

                if (!$donation->confirmed) {
                    Message::Error(Text::get('dashboard-donor-pdf_closed', $year));
                    throw new Redirection('/dashboard/activity/donor');
                }

                // ver si es una persona juridica
                $donation->nif_type = '';
                $donation->valid_nif = Check::nif($donation->nif, $donation->nif_type);
                $donation->juridica = ($donation->nif_type == 'cif' || $donation->nif_type == 'vat');

                // verificar que el nif es correcto
                if ($donation->valid_nif === false) {
                    Message::Error(Text::get('validate-project-value-contract_nif'));
                    throw new Redirection('/dashboard/activity/donor');
                }

                if (empty($donation->name)
                    || ( !$donation->juridica && empty($donation->surname) )
                    || empty($donation->nif)
                    || empty($donation->address)
                    || empty($donation->zipcode)
                    || empty($donation->location)
                    || empty($donation->region)
                    || empty($donation->country)
                ) {
                    Message::Error(Text::get('validate-donor-mandatory'));
                    throw new Redirection('/dashboard/activity/donor');
                }

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

                $pdf = PDF::donativeCert($donation);

                if ($debug) {
                    header('Content-type: text/html');
                    echo 'FIN';
                    echo '<hr><pre>' . print_r($pdf, true) . '</pre>';
                }

                // y se lo damos para descargar
                echo $pdf->Output($filename, 'D');

                die;

            }
            // fin action download

            return $donation;

        }

    }

}
