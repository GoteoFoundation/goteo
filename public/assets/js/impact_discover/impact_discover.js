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
    let footprints = ods.footprints;
    return footprints.indexOf(footprint)>=0 ? true : false;
}

// add ODS to ODS select
function addODSToSelect(ods) {
    $(".impact-discover-filters select").append('<option data-footprints="'+ods.footprints+'">'+ods.ods+'</option>');
}

function resetODSIcons() {
    $("#odsicons").html("");
}

// add ODS to icon list
function addODSIcon(ods) {
    $("#odsicons").append('<img src="./assets/img/ods/'+ods.id+'.png" />');
}

function resetFootprints() {
    $("a[data-footprint]").removeClass("active");
}

// activate footprints by ods select
function activateFootprints(footprints) {
    $.each(footprints, function(key, footprint){
        $('a[data-footprint="'+footprint+'"]').addClass("active");
    })
}

// filter ODS select options by footprint
function filterODSSelectOptionsByFootprint (footprint) {
    resetODSSelect();
    resetODSIcons();
    let options = odsList.ods.filter(ods => {
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
    footprint = $(this).attr("data-footprint");
    filterODSSelectOptionsByFootprint(footprint);
});

// bind ODS select change
$(".impact-discover-filters").on("change","select", function(e){
    ods = $(this).find("option:selected").attr("data-footprints").split(",");
    filterFootprintByODS(ods);
});

