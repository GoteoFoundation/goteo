<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/**
 *   Workshop
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Model\Call;
use Goteo\Model\User;
use Goteo\Model\Workshop;
use Goteo\Model\Workshop\WorkshopLocation;


class WorkshopSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nuevo taller',
      'edit' => 'Editando taller'
    );


    static protected $label = 'Talleres';

     /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(User $user, $node): bool {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id = null) {

        if(is_null($id)) $workshop = new Workshop;
        else             $workshop = Workshop::get($id);
        $location = WorkshopLocation::get($workshop);
        if(!$location) $location = new WorkshopLocation();
        // print_r($location);die;

        if ($workshop && $this->isPost()) {
            // print_r($this->getPost('radius').$this->getPost('latitude'));die;
            try {
                $workshop->title       = $this->getPost('title');
                $workshop->description       = $this->getPost('description');
                $workshop->schedule       = $this->getPost('schedule');
                $workshop->url       = $this->getPost('url');
                $workshop->date_in       = $this->getPost('date_in');
                $workshop->date_out       = $this->getPost('date_out');
                $workshop->call_id       = $this->getPost('call_id');

                if ($workshop->save($errors)) {
                    if($this->getPost('latitude') && $this->getPost('longitude')) {
                        $loc = new WorkshopLocation(array(
                            'id'           => $workshop->id,
                            'city'         => $this->getPost('city'),
                            'region'       => $this->getPost('region'),
                            'country'      => $this->getPost('country'),
                            'country_code' => $this->getPost('country'),
                            'longitude'    => $this->getPost('longitude'),
                            'latitude'     => $this->getPost('latitude'),
                            'radius'       => $this->getPost('radius'),
                            'method'       => 'manual'
                        ));
                        $errors = [];
                        if ($loc->save($errors)) {
                            Message::info('Localización actualizada a '.$this->getPost('city') .', '.$this->getPost('country'));
                            return $this->redirect();
                        } else {
                            Message::error(implode("<br>", $errors));
                        }
                    }
                    else {
                        Message::error('Error: geolocalización no cambiada!');
                    }

                }
                else {
                    Message::error(implode(', ', $errors));
                }

            } catch(ModelException $e) {
                Message::error('Workshop save failed: ' . $e->getMessage());
            }
        }

        $calls = [null => '---------------'];
        foreach(Call::getList(array(), 0, 100) as $call) {
            $calls[$call->id] = $call->name;
        }
        // print_r($workshop);
        return array(
            'template' => 'admin/generic_edit',
            'location' => $location,
            'radius' => $location->radius,
            'with_radius' => true,
            'data' => $workshop,
            'form' => [
                'action' => static::getUrl($id ? "edit/$id" : 'add'),
                'submit' => [
                    'name' => 'add',
                    'label' => Text::get('regular-save')
                ],
                'fields' => [
                    'id' => [
                        'name' => 'id',
                        'type' => 'hidden',
                    ],
                    'title' => [
                        'label' => Text::get('regular-title'),
                        'name' => 'title',
                        'type' => 'text',
                        'properties' => 'size=73'
                    ],
                    'description' => [
                        'label' => Text::get('regular-description'),
                        'name' => 'description',
                        'type' => 'textarea',
                        'properties' => 'cols=70'
                    ],
                    'url' => [
                        'label' => Text::get('regular-url'),
                        'name' => 'url',
                        'type' => 'text',
                        'properties' => 'size=73'
                    ],
                    'schedule' => [
                        'label' => Text::get('regular-schedule'),
                        'name' => 'schedule',
                        'type' => 'text',
                        'properties' => 'size=73'
                    ],
                    'date_in' => [
                        'label' => Text::get('regular-date_in'),
                        'name' => 'date_in',
                        'type' => 'date',
                        'properties'  => 'class="datepicker"',
                    ],
                    'date_out' => [
                        'label' => Text::get('regular-date_out'),
                        'name' => 'date_out',
                        'type' => 'date',
                        'properties'  => 'class="datepicker"',
                    ],
                    'call_id' => [
                        'label' => 'Convocatoria',
                        'name' => 'call_id',
                        'type' => 'select',
                        'values' => $calls
                    ],
                ]
            ]
        );
    }


    public function addAction() {
        return $this->editAction();
    }


    public function listAction() {

        $workshops = Workshop::getAll();

        return array(
                'template' => 'admin/generic_list',
                'addbutton' => Text::get('regular-add'),
                'data' => $workshops,
                'url' => self::getUrl(),
                'model' => 'workshop',
                'columns' => [
                    'id' => 'Id',
                    'title' => Text::get('regular-title'),
                    'description' => Text::get('regular-description'),
                    'schedule' => Text::get('regular-schedule'),
                    'url' => Text::get('regular-url'),
                    'date_in' => Text::get('regular-date_in'),
                    'date_out' => Text::get('regular-date_out'),
                    'call_id' => 'Convocatoria',
                    'edit' => '',
                    'translate' => '',
                    'remove' => ''
                ]
        );
    }


    public function removeAction($id = null) {

        $workshop = Workshop::get($id);

        if ($workshop->dbDelete()) {
            Message::info('Taller eliminado correctamente');
        } else {
            Message::error('No se ha podido eliminar el taller');
        }
        return $this->redirect();

    }



}
