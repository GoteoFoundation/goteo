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

$(function () {
    var filter;
    var newFilter;

    var form = $('#filter-form');
    var title = $('#form-title');
    var startdate = $('#form-startdate');
    var enddate = $('#form-enddate');
    var predefineddate = $('#form-predefineddata');
    var role = $('#form-role');
    var projects = $('#form-projects');
    var calls = $('#form-calls');
    var channels = $('#form-channels');
    var matchers = $('#form-matchers');
    var sdgs = $('#form-sdgs');
    var footprints = $('#form-footprints');
    var status = $('#form-status');
    var typeofdonor = $('#form-typeofdonor');
    var foundationdonor = $('#form-foundationdonor');
    var wallet = $('#form-wallet');
    var cert = $('#form-cert');
    var location = $('#form-project_location');

    function changeForm(role){

        if (role == "user" || role == "test") {
            startdate.hide();
            enddate.hide();
            predefineddate.hide();
            projects.hide();
            calls.hide();
            channels.hide();
            matchers.hide();
            sdgs.hide();
            footprints.hide();
            status.hide();
            status.val = '';
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            location.hide();
            location.val = '';
        }
        else if (role == "donor") {
            startdate.show();
            enddate.show();
            predefineddate.show();
            projects.show();
            calls.show();
            channels.show();
            matchers.show()
            footprints.show();
            sdgs.show();
            status.show();
            typeofdonor.show();
            foundationdonor.show();
            cert.show();
            wallet.show();
            location.show();
        }
        else if (role == "no-donor") {
            startdate.show();
            enddate.show();
            predefineddate.show();
            projects.hide();
            calls.hide();
            channels.hide();
            matchers.hide()
            footprints.hide();
            sdgs.hide();
            status.hide();
            typeofdonor.hide();
            foundationdonor.show();
            cert.hide();
            wallet.show();
            location.hide();
        }
        else if (role == "promoter") { 
            startdate.show();
            enddate.show();
            predefineddate.show();
            projects.show();
            calls.show();
            channels.show();
            matchers.show();
            sdgs.show();
            footprints.show();
            status.show();
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            location.show();
        }
        else if (role == "matcher") {
            startdate.show();
            enddate.show();
            predefineddate.show();
            projects.hide();
            calls.hide();
            channels.hide();
            footprints.hide();
            sdgs.hide();
            status.hide();
            status.val = '';
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            location.hide();
            location.val = '';
            matchers.show();
        }
    }

    function changeDates(dates){

        var today = new Date();

        if (dates == 0) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(7, 'days').format('DD/MM/YYYY'); 
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        }
        else if (dates == 1) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(30, 'days').format('DD/MM/YYYY');
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        } else if (dates == 2) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(365, 'days').format('DD/MM/YYYY'); 
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        } else if (dates == 3) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + today.getFullYear();
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        } else if (dates == 4) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + (today.getFullYear()-1);
            document.getElementById('autoform_enddate').value = '31/12/' + (today.getFullYear()-1);
        } else if (dates == 5) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + (today.getFullYear()-2);
            document.getElementById('autoform_enddate').value = '31/12/' + (today.getFullYear()-2);
        }
    }

    if (document.getElementById('autoform')){

        document.getElementById('autoform_role').onchange = 
            function(){ 
                changeForm(this.value); 
            };

        document.getElementById('autoform_predefineddata').onchange =
            function(){
                changeDates(this.value);
            };

        document.getElementById('autoform_role').onchange();
    }
});