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

function resetODSIcons() {
    $('.impact-discover-projects h1').text("Busca un proyecto por Huellas o ODS");
    $("#odsicons").html("");
}

// add ODS to icon list
function addODSIcon(ods) {
    $('.impact-discover-projects h1').text("Proyectos que cumplen con los siguientes ODS:");
    var icon = '<img src="./assets/img/ods/'+ods.id+'.png" />';
    var removeIcon = '<a class="close flip" href="#backflip-l-artiga-coop-a-punt"><i class="icon icon-close"></i></a>';
    $("#odsicons").append('<div class="odsicon" data-ods="'+ods.id+'">'+icon+removeIcon+'</div>');
    activateODS(ods);
}

//activate ODS on JSON
function activateODS(ods){
    console.log(ods);
}

//remove ODS
function removeODS(ods){
    console.log(ods);
    $('.odsicon[data-ods="'+ods+'"]').remove();
}

//reset footprints active status
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
function filterODSSelectOptionsByFootprint (footprint) {
    resetODSSelect();
    resetODSIcons();
    var options = odsList.ods.filter(ods => {
        return hasFootprint(ods,footprint) ? ods.ods : false ;
    });
    $.each(options,function(key,option){
        if (option) {
            addODSToSelect(option);
            addODSIcon(option);
        }
    });
}

// filter footprints by ODS
function filterFootprintByODS (ods) {
    resetFootprints();
    resetODSIcons();
    activateFootprints(ods);
}

// bind click on footprint
$(".impact-discover-filters").on("click","a", function(e){
    e.preventDefault();
    resetFootprints();
    footprint = $(this).attr("data-footprint");
    filterODSSelectOptionsByFootprint(footprint);
    $(this).addClass("active");
});

// bind ODS select change
$(".impact-discover-filters").on("change","select", function(e){
    ods = $(this).find("option:selected").attr("data-footprints").split(",");
    filterFootprintByODS(ods);
});

//bind ODS close icon
$("#odsicons").on("click",".close", function(e){
    e.preventDefault();
    ods = $(this).parent().attr("data-ods");
    removeODS(ods);
})

