/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/

$(function(){
  

  var projectIcon = L.icon({
    iconUrl: '/assets/img/map/pin-project.svg',
    iconSize:     [38, 95] // size of the icon
  });

  var workshopIcon = L.icon({
    iconUrl: '/assets/img/map/pin-workshop.svg',
    iconSize:     [38, 95] // size of the icon
  });

  // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  //     attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  // }).addTo(map);
  
  var channel = $('#map').data('channel');
  var matcher = $('#map').data('matcher');
  var geojson = $('#map').data('geojson');
  var zoom = $('#map').data('zoom');
  var center = $('#map').data('center');

  var map = L.map('map', {
    fullscreenControl: true,
    center: center ? center : [0,0],
    zoom: zoom? zoom : 3
  });

  L.tileLayer($('#map').data('tile-layer'), {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);


  if (geojson) {
    $.getJSON(geojson, function(data) {
      geojsonLayer = L.geoJson(data).addTo(map);
      // map.fitBounds(geojsonLayer.getBounds());
    })
  }

  if (channel) {
    $.ajax({
      url: '/api/map/channel/' + channel,
      type: 'GET'
    }).done(function(data) {
      var latlngs = [];
      var projects = data.projects;
      var workshops = data.workshops;
      var project_markers = L.markerClusterGroup();
      projects.forEach(function(project){

        if (project.project_location.latitude && project.project_location.longitude) {
          latlngs.push([project.project_location.latitude, project.project_location.longitude]);
          project_markers.addLayer(L.marker([project.project_location.latitude, 
            project.project_location.longitude], { icon: projectIcon }).bindPopup(project.popup, { width: 340 }));
        }
      });
        
      var workshop_markers = L.markerClusterGroup();
      workshops.forEach(function(workshop){
      if (workshop.workshop_location.latitude && workshop.workshop_location.longitude) {
        latlngs.push([workshop.workshop_location.latitude, workshop.workshop_location.longitude]);
        workshop_markers.addLayer(L.marker([workshop.workshop_location.latitude, 
          workshop.workshop_location.longitude], { icon: workshopIcon }).bindPopup(workshop.popup, { width: 340 }));
        }
      });
        
      if (latlngs.length) {
        var latLngBounds = L.latLngBounds(latlngs);
        // map.fitBounds(latLngBounds);
      }

      if (projects.length) {
        map.addLayer(project_markers);
        $('#button-projects-hide').removeClass('hidden');
      }

      if (workshops.length) {
        map.addLayer(workshop_markers);
        $('#button-workshops-hide').removeClass('hidden');
      }

      // map.fitBounds(latLngBounds);

      $('#button-projects-activate').click(function() {
        map.addLayer(project_markers);
        $('#button-projects-hide').removeClass('hidden');
        $(this).addClass('hidden');
      });

      $('#button-workshops-activate').click(function() {
        map.addLayer(workshop_markers);
        $('#button-workshops-hide').removeClass('hidden');
        $(this).addClass('hidden');
      });

      $('#button-projects-hide').click(function() {
        map.removeLayer(project_markers);
        $('#button-projects-activate').removeClass('hidden');
        $(this).addClass('hidden');
      });

      $('#button-workshops-hide').click(function() {
        map.removeLayer(workshop_markers);
        $('#button-workshops-activate').removeClass('hidden');
        $(this).addClass('hidden');
      });
    });
  }

  if (matcher) {
    $.ajax({
      url: '/api/map/matcher/' + matcher,
      type: 'GET'
    }).done(function(data) {
      var latlngs = [];
      var projects = data.projects;
      var project_markers = L.markerClusterGroup();
      projects.forEach(function(project){

        if (project.project_location.latitude && project.project_location.longitude) {
          latlngs.push([project.project_location.latitude, project.project_location.longitude]);
          project_markers.addLayer(L.marker([project.project_location.latitude, 
            project.project_location.longitude], { icon: projectIcon }).bindPopup(project.popup, { width: 340 }));
        }
      });
        
      if (latlngs.length) {
        var latLngBounds = L.latLngBounds(latlngs);
        // map.fitBounds(latLngBounds);
      }

      if (projects.length) {
        map.addLayer(project_markers);
        $('#button-projects-hide').removeClass('hidden');
      }

      $('#button-projects-activate').click(function() {
        map.addLayer(project_markers);
        $('#button-projects-hide').removeClass('hidden');
        $(this).addClass('hidden');
      });

      $('#button-projects-hide').click(function() {
        map.removeLayer(project_markers);
        $('#button-projects-activate').removeClass('hidden');
        $(this).addClass('hidden');
      });
    });
  }
});