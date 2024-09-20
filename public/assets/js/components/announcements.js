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

$(function() {
    const $closeAnnouncement = document.getElementById('announcement-close');
    const $announcements = $closeAnnouncement.parentNode;
    const announcementsId = $announcements.id;
    const date = new Date();
    const today = `${date.getFullYear()}-${date.getMonth()}-${date.getDay()}`;
    const itemId = `goteo_announcements_${today}_${announcementsId}`;

    $closeAnnouncement.addEventListener("click", (event) => {
        $announcements.style.display = 'none';
        localStorage.setItem(itemId, true);
    });

    if (!localStorage.getItem(itemId))
        $announcements.style.display = 'block';

    $(".slider-announcements").slick({
        dots: false,
        infinite: true,
        autoplay: false,
        autoplaySpeed: 7000,
        speed: 500,
        fade: true,
        cssEase: "linear",
        responsive: [
            {
                breakpoint: 500,
                settings: {
                    dots: true,
                    arrows: false
                }
            }
        ]
    });
});
