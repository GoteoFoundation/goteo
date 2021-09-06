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
  

  let projectIcon = L.icon({
    iconUrl: '/assets/img/map/pin-project.svg',
    iconSize:     [38, 95] // size of the icon
  });

  let workshopIcon = L.icon({
    iconUrl: '/assets/img/map/pin-workshop.svg',
    iconSize:     [38, 95] // size of the icon
  });

  let markers = {
    projects: undefined,
    workshops: undefined
  };

  const channel = $('#map').data('channel');
  const matcher = $('#map').data('matcher');
  const geojson = $('#map').data('geojson');
  const zoom = $('#map').data('zoom');
  const center = $('#map').data('center');

  const map = L.map('map', {
    fullscreenControl: true,
    center: center ? center : [0,0],
    zoom: zoom? zoom : 3,
    maxZoom: 20
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
      this.project_markers = L.markerClusterGroup();
      projects.forEach(function(project){

        if (project.project_location.latitude && project.project_location.longitude) {
          latlngs.push([project.project_location.latitude, project.project_location.longitude]);
          project_markers.addLayer(L.marker([project.project_location.latitude, 
            project.project_location.longitude], { icon: projectIcon }).bindPopup(project.popup, { width: 340 }));
        }
      });
        
      workshop_markers = L.markerClusterGroup();
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
        map.addLayer(project_markers, 'projects');
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
      project_markers = L.markerClusterGroup();
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

  function loadData(data) {
    var latlngs = [];
    var projects = data.projects;
    var workshops = data.workshops;
    project_markers = L.markerClusterGroup();
    
    if (projects) {
      projects.forEach(function(project){
        if (project.project_location.latitude && project.project_location.longitude) {
          latlngs.push([project.project_location.latitude, project.project_location.longitude]);
          project_markers.addLayer(L.marker([project.project_location.latitude, 
            project.project_location.longitude], { icon: projectIcon }).bindPopup(project.popup, { width: 340 }));
        }
      });

      map.addLayer(project_markers);
      markers.projects = project_markers;
      $('#button-projects-hide').removeClass('hidden');
    }
      
    if (workshops) {
      workshop_markers = L.markerClusterGroup();
      workshops.forEach(function(workshop){
      if (workshop.workshop_location.latitude && workshop.workshop_location.longitude) {
        latlngs.push([workshop.workshop_location.latitude, workshop.workshop_location.longitude]);
        workshop_markers.addLayer(L.marker([workshop.workshop_location.latitude, 
          workshop.workshop_location.longitude], { icon: workshopIcon }).bindPopup(workshop.popup, { width: 340 }));
        }
      });

      map.addLayer(workshop_markers);
      markers.workshops = workshop_markers
      $('#button-workshops-hide').removeClass('hidden');
    }
      
    if (latlngs.length) {
      map.invalidateSize();

      var latLngBounds = L.latLngBounds(latlngs);
      map.fitBounds(latLngBounds);
    }

    $('#button-projects-activate').on('click', (function() {
      map.addLayer(project_markers);
      $('#button-projects-hide').removeClass('hidden');
      $(this).addClass('hidden');
    }));

    $('#button-workshops-activate').on('click', (function() {
      map.addLayer(workshop_markers);
      $('#button-workshops-hide').removeClass('hidden');
      $(this).addClass('hidden');
    }));

    $('#button-projects-hide').on('click', (function() {
      map.removeLayer(project_markers);
      $('#button-projects-activate').removeClass('hidden');
      $(this).addClass('hidden');
    }));

    $('#button-workshops-hide').on('click', (function() {
      map.removeLayer(workshop_markers);
      $('#button-workshops-activate').removeClass('hidden');
      $(this).addClass('hidden');
    }));
  }

  function cleanMap() {

    if (markers.projects) {
      map.removeLayer(markers.projects);
      markers.projects = undefined;
    }
      
    
    if (markers.workshops) {
      map.removeLayer(markers.workshops);
      markers.workshops = undefined;
    }
  }

  $('#map').on("goteo_map_add_projects", function(event) { 
    cleanMap();
    loadData(JSON.parse($('#map').attr('data-projects')));
  });
});