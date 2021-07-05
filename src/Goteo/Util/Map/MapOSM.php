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
          $channel,
          $matcher,
          $projects,
          $workshops,
          $donations,
          $geojson,
          $center,
          $zoom;


  public function __construct($h, $w = "100%") {
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

  public function setChannel($channel) {
    $this->channel = $channel;
  }

  public function setMatcher($matcher) {
    $this->matcher = $matcher;
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

  public function setGeoJSON($geojson) {
    $this->geojson = $geojson;
  }

  public function setZoom($zoom) {
    $this->zoom = $zoom;
  }

  public function setCenter($center = array()) {
    $this->center = $center;
  }
  
  public function getChannel() {
    return $this->channel;
  }

  public function getMatcher() {
    return $this->matcher;
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

  public function getGeoJSON() {
    return $this->geojson;
  }

  public function getZoom() {
    return $this->zoom;
  }

  public function getCenter() {
    return $this->center;
  }

  public function map() {
    return View::render('partials/utils/map/map_canvas', ['map'  => $this]);
  }

 }