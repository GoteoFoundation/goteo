jQuery(document).ready(function ($) {
    /* Simular saltos entre nodos como enlaces */
    $(".node-jump").click(function (event) {
        event.preventDefault();
        location.href = '/jump.php?action=go&url=' + escape($(this).attr('href'));
    });
});
