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
    let filter;
    let newFilter;

    const form = $('#filter-form');
    const title = $('#form-title');
    const startdate = $('#form-startdate');
    const enddate = $('#form-enddate');
    const predefineddate = $('#form-predefineddata');
    const role = $('#form-role');
    const projects = $('#form-projects');
    const calls = $('#form-calls');
    const channels = $('#form-channels');
    const matchers = $('#form-matchers');
    const sdgs = $('#form-sdgs');
    const social_commitments = $('#form-social_commitments');
    const footprints = $('#form-footprints');
    const project_status = $('#form-project_status');
    const invest_status = $('#form-invest_status');
    const amount = $('#form-amount');
    const typeofdonor = $('#form-typeofdonor');
    const foundationdonor = $('#form-foundationdonor');
    const wallet = $('#form-wallet');
    const cert = $('#form-cert');
    // const project_location = $('#form-project_location');
    // const donor_location = $('#form-donor_location');
    const filter_location = $('#form-filter_location');
    const donor_status = $('#form-donor_status')

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
            social_commitments.hide();
            project_status.hide();
            project_status.val = '';
            invest_status.hide();
            invest_status.val = '';
            amount.hide();
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            donor_status.hide();
            donor_status.val = '';
            // project_location.hide();
            // project_location.val = '';
            // donor_location.hide();
            // donor_location.val = '';
            filter_location.show();
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
            social_commitments.show();
            sdgs.show();
            project_status.show();
            invest_status.show();
            amount.show();
            typeofdonor.show();
            foundationdonor.show();
            cert.show();
            wallet.show();
            donor_status.show();
            // project_location.hide();
            // project_location.val = '';
            // donor_location.show();
            filter_location.show();
        }
        else if (role == "no-donor") {
            startdate.show();
            enddate.show();
            predefineddate.show();
            projects.hide();
            calls.hide();
            channels.hide();
            matchers.hide();
            footprints.hide();
            social_commitments.hide();
            sdgs.hide();
            project_status.hide();
            invest_status.hide();
            amount.hide();
            typeofdonor.hide();
            foundationdonor.show();
            cert.hide();
            wallet.show();
            donor_status.hide();
            donor_status.val = '';
            // project_location.hide();
            // donor_location.hide();
            // donor_location.val = '';
            filter_location.hide();
            filter_location.val = '';
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
            social_commitments.hide();
            project_status.show();
            invest_status.hide();
            amount.hide();
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            donor_status.hide()
            donor_status.val = '';
            // project_location.show();
            filter_location.show();
            // donor_location.hide();
            // donor_location.val = '';
        }
        else if (role == "matcher") {
            startdate.show();
            enddate.show();
            predefineddate.show();
            projects.hide();
            calls.hide();
            channels.hide();
            footprints.hide();
            social_commitments.hide();
            sdgs.hide();
            project_status.hide();
            project_status.val = '';
            invest_status.hide();
            invest_status.val = '';
            amount.hide();
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            donor_status.hide()
            donor_status.val = '';
            // project_location.hide();
            // project_location.val = '';
            // donor_location.hide();
            // donor_location.val = '';
            filter_location.hide();
            filter_location.val = '';
            matchers.show();
        }
    }

    function changeDates(dates){

        const today = new Date();

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
