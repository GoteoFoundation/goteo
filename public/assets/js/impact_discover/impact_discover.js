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

//reset ODS JSON
function resetODS() {
    $.each(odsList.ods, function(key, value){
        odsList.ods[key]["active"] = false
    })
}

// reset ODS select
function resetODSSelect() {
    $(".impact-discover-filters select option").remove();
    $(".impact-discover-filters select").append("<option>Filtra por Objetivos de Desarrollo sostenible</option");
}

// check if ODS option has selected footprint
function hasFootprint (ods, footprint) {
    var footprints = ods.footprints;
    return footprints.indexOf(footprint)>=0 ? true : false;
}

// add ODS to ODS select
function addODSToSelect(ods) {
    $(".impact-discover-filters select").append('<option data-footprints="'+ods.footprints+'">'+ods.ods+'</option>');
}

// reset ODS icons
function resetODSIcons() {
    $('.impact-discover-projects h1').text("Busca un proyecto por Huellas o ODS");
    $("#ods-icons .col").html("");
}

// add ODS to icon list
function addODSIcon(ods) {
    $('.impact-discover-projects h1').text("Proyectos que cumplen con los siguientes ODS:");
    var icon = '<img src="./assets/img/ods/'+ods.id+'.svg" />';
    var removeIcon = '<a class="close flip" href="#"><i class="icon icon-close"></i></a>';
    $("#ods-icons .col").append('<div class="odsicon" data-ods="'+ods.id+'">'+icon+removeIcon+'</div>');
}

// activate ODS on JSON
function activateODS(ods){
    $.each(odsList.ods, function(key, value){
        if (value.id == ods.id) odsList.ods[key]["active"] = true
    })
    addODSToSelect(ods);
}

// remove ODS
function removeODS(ods){
    $.each(odsList.ods, function(key, value){
        if (value.id == ods) odsList.ods[key]["active"] = false
    })
    refreshODS();
}

function isODSActive(ods) {
    return ods.active;
}

// check ODS option for mobile list
function checkODSoption(ods){
    $('input[name="'+ods.id+'"]').prop("checked",true);
}


// reset ODS options for mobile list
function resetODScheckboxes() {
    $('#filters-ods-list input[type=checkbox]').prop("checked",false);
}

// get active ODS
function getActiveODS(){
    var odsActive = odsList.ods.filter(function(ods) {
        return isODSActive(ods) ? ods.ods : false ;
    });
    return odsActive;
}

// refresh ODS icons
function refreshODS() {
    resetODSIcons();
    resetODScheckboxes();
    var odsActive = getActiveODS();

    var odsArray = [];

    $.each(odsActive, function(key,ods){
        addODSIcon(ods);
        checkODSoption(ods);
        odsArray.push(ods.id);
    });

    resetProjects();
    loadProjects(odsArray);
}

function resetProjects() {
    $('.impact-discover-projects > div.container > div:not(#ods-icons)').remove();
}

function loadProjects(ods) {
    $('.impact-discover-projects > div.container').after('<div class="loading-container">Loading</div>');
    var url = "https://goteo-seven.vercel.app/ods.html?ods=";
    var url = url+ods.join(",");
    $.get( url, function( data ) {
        $('.impact-discover-projects > div.container').append( data );
      })
      .done(function(){
        $('.loading-container').remove();
      });
}

// reset footprints active status
function resetFootprints() {
    $("a[data-footprint]").removeClass("active");
}

// activate footprints by ods selected
function activateFootprints(footprints) {
    $.each(footprints, function(key, footprint){
        $('a[data-footprint="'+footprint+'"]').addClass("active");
    })
}

// filter ODS select options by footprint
function filterODSByFootprint (footprint) {
    resetODSSelect();
    resetODS();
    var options = odsList.ods.filter(function(ods) {
        return hasFootprint(ods,footprint) ? ods.ods : false ;
    });
    $.each(options,function(key,option){
        if (option) {
            activateODS(option);
        }
    });
    refreshODS();
}

// filter footprints by ODS
function filterFootprintByODS (ods) {
    resetFootprints();
    resetODSIcons();
    activateFootprints(ods);
}

function expandMobileFilter() {
    $("#filters-footprints").slideDown();
    $("#filters-ods-list").slideDown();
    $("#filters-mobile").css({"width":"100%"});
    $("#filters-mobile .close").show();
}

function contractMobileFilter() {
    $("#filters-footprints").slideUp();
    $("#filters-ods-list").slideUp();
    $("#filters-mobile .close").hide();
    $("#filters-mobile").css({"width":"50%"});
}

function needsScroll() {
    var $last = $('.impact-discover-projects div:last');
    return $(window).scrollTop() >= $last.offset().top - $last.height()
}

$(window).on('resize scroll', function() {
    if(needsScroll() && !$('.loading-container').length) {
        ods = [];
        //TODO: find selected ODS
        var odsActive = getActiveODS();

        var odsArray = [];

        $.each(odsActive, function(key,ods){
            odsArray.push(ods.id);
        });
        loadProjects(odsArray);
    };
});

// bind click on footprint
$("#filters-footprints").on("click","a", function(e){
    e.preventDefault();
    resetFootprints();
    footprint = $(this).attr("data-footprint");
    filterODSByFootprint(footprint);
    $(this).addClass("active");
    if ($(window).width()<721) {
        $("#filters-footprints").slideUp();
        contractMobileFilter();
    }
    $("#filters-ods-list").slideUp();
});

// bind ODS select change
$(".impact-discover-filters").on("change","select", function(e){
    ods = $(this).find("option:selected").attr("data-footprints").split(",");
    filterFootprintByODS(ods);
});

// bind ODS close icon
$("#ods-icons").on("click",".close", function(e){
    e.preventDefault();
    ods = $(this).parent().attr("data-ods");
    removeODS(ods);
})

// bind filter mobile
$("#filters-mobile").on("click","a", function(e){
    e.preventDefault();
    expandMobileFilter();
})

// bind reset ods on mobile list
$("#filters-ods-list").on("click","a#reset-ods", function(e){
    e.preventDefault();
    resetODScheckboxes();
});

// bind submit button on ods list (mobile)
$("#filters-ods-list").on("click","button",function(e){
    $('#filters-ods-list input').each(function(key,checkbox){
        if (checkbox.checked) {
            var ods = {}
            ods.id = checkbox.name;
            activateODS(ods);
        }
    });
    refreshODS();
    contractMobileFilter();
});

// bind close icon at mobile filters
$("#filters-mobile").on("click","a.close",function(e){
    e.preventDefault();
    contractMobileFilter();
});
