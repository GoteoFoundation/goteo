<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

 namespace Goteo\Util\Map;

 use Goteo\Application\Config;
 use Goteo\Application\View;

 class MapOSM {

  private $tile_layer,
          $height,
          $width,
          $projects,
          $workshops,
          $donations;


  public function __construct($h, $w) {
    $this->tile_layer = Config::get('map.open_street_maps.tile_layer');
    $this->height = $h;
    $this->width = $w;
  }
  
  public function getTileLayer() {
    return $this->tile_layer;
  }

  public function getWidth() {
    return $this->width;
  }

  public function getHeight() {
    return $this->height;
  }

  public function setProjects($projects) {
    $this->projects = $projects;
  }

  public function setWorkshops($workshops) {
    $this->workshops = $workshops;
  }

  public function setDonations($donations) {
    $this->donations = $donations;
  }

  public function getProjects() {
    return $this->projects;
  }

  public function getWorkshops() {
    return $this->workshops;
  }

  public function getDonations() {
    return $this->donations;
  }

  public function map() {
    return View::render('partials/utils/map/map_canvas', ['map'  => $this]);
  }

 }