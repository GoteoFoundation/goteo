<?php
$this->layout("layout", [
    'bodyClass' => 'about',
    'title' => $this->name,
    'meta_description' => $this->description
    ]);

$this->section('content');
?>

    <div id="main">

        <div class="widget">
            <h3 class="title">Javascript licenses</h3>

        <table id="jslicense-labels1">
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/jquery-1.6.4.min.js">jquery-1.6.4.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/jquery-1.6.4.js">jquery-1.6.4.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.tipsy.min.js">jquery.tipsy.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.tipsy.js">jquery.tipsy.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.fancybox.min.js">jquery.fancybox.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.fancybox-1.3.4.js">jquery.fancybox-1.3.4.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.slides.min.js">jquery.slides.min.js</a></td>
                <td><a href="http://www.apache.org/licenses/LICENSE-2.0">Apache-2.0</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.slides-1.1.8.js">jquery.slides-1.1.8.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.mousewheel.min.js">jquery.mousewheel.min.js</a></td>
                <td><a href="http://www.apache.org/licenses/LICENSE-2.0">Apache-2.0</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.mousewheel-3.0.4.js">jquery.mousewheel-3.0.4.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.jscrollpane.min.js">jquery.jscrollpane.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/jquery.jscrollpane-2.0.23.js">jquery.jscrollpane-2.0.23.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/calendar/moment.min.js">moment.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/calendar/moment-2.9.js">moment-2.9.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/calendar/fullcalendar.js">fullcalendar.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/calendar/fullcalendar.js">fullcalendar-2.2.7.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/datepicker.min.js">datepicker.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/datepicker.js">datepicker.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/sha1.min.js">sha1.min.js</a></td>
                <td><a href="http://opensource.org/licenses/BSD-3-Clause">BSD-3-Clause</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/sha1.js">sha1.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/ckeditor/ckeditor.js">ckeditor.js</a></td>
                <td><a href="http://www.gnu.org/licenses/gpl-2.0.html">GPL-2.0</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/ckeditor/_source/core/ckeditor.js">ckeditor.js</a></td>
            </tr>

<!--
TODO:
 <script src="<?= SRC_URL ?>/assets/vendor/jquery.mobile.custom.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= SRC_URL ?>/assets/vendor/pronto/jquery.fs.pronto.min.js"></script>

<script src="<?= SRC_URL ?>/assets/vendor/clipboard/clipboard.min.js"></script>


 -->

            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/jquery-1.12.4.min.js">jquery-1.12.4.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/jquery-1.12.4.js">jquery-1.12.4.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/jquery.mobile.custom.min.js">jquery.mobile.custom.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/jquery.mobile-1.4.5.js">jquery.mobile-1.4.5.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/pronto/jquery.fs.pronto.min.js">jquery.fs.pronto.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/pronto/jquery.fs.pronto-3.2.1.js">jquery.fs.pronto-3.2.1.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/typeahead/jquery.typeahead.min.js">jquery.typeahead.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/typeahead/jquery.typeahead.js">jquery.typeahead.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/d3/d3.min.js">d3.min.js</a></td>
                <td><a href="http://opensource.org/licenses/BSD-3-Clause">BSD-3-Clause</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/d3/d3.js">d3.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/footable/footable.min.js">footable.min.js</a></td>
                <td><a href="http://www.gnu.org/licenses/gpl-3.0.html">GPL-3.0</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/footable/footable.js">footable.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/clipboard/clipboard.min.js">clipboard.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/clipboard/clipboard-1.5.16.js">clipboard-1.5.16.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/bootstrap/bootstrap.min.js">bootstrap.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/bootstrap/bootstrap-3.3.5.js">bootstrap-3.3.5.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/datepicker/js/zebra_datepicker.js">zebra_datepicker.js</a></td>
                <td><a href="http://www.gnu.org/licenses/lgpl-3.0.html">LGPL-3.0</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/datepicker/js/zebra_datepicker.src.js">zebra_datepicker.src.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.js">simplemde.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/simplemde/src/js/simplemde.js">simplemde.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/vendor/summernote/summernote.min.js">summernote.min.js</a></td>
                <td><a href="http://www.jclark.com/xml/copying.txt">Expat</a></td>
                <td><a href="<?= SRC_URL ?>/assets/vendor/summernote/summernote.js">summernote.js</a></td>
            </tr>

            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/superform.js">superform.js</a></td>
                <td><a href="http://www.gnu.org/licenses/agpl-3.0.html">AGPL-3.0-or-later</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/superform.js">superform.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/view/js/calendar/home_calendar.js">home_calendar.js</a></td>
                <td><a href="http://www.gnu.org/licenses/agpl-3.0.html">GNU-Affero-GPL-3.0-or-later</a></td>
                <td><a href="<?= SRC_URL ?>/view/js/calendar/home_calendar.js">home_calendar.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/js/goteo.js">goteo.js</a></td>
                <td><a href="http://www.gnu.org/licenses/agpl-3.0.html">AGPL-3.0-or-later</a></td>
                <td><a href="<?= SRC_URL ?>/assets/js/goteo.js">goteo.js</a></td>
            </tr>
            <tr>
                <td><a href="<?= SRC_URL ?>/assets/js/geolocation.js">geolocation.js</a></td>
                <td><a href="http://www.gnu.org/licenses/agpl-3.0.html">AGPL-3.0-or-later</a></td>
                <td><a href="<?= SRC_URL ?>/assets/js/geolocation.js">geolocation.js</a></td>
            </tr>
        </table>

        </div>

    </div>

<?php $this->replace() ?>

