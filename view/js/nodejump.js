jQuery(document).ready(function ($) {
    /* Simular saltos entre nodos como enlaces */
    $(".node-jump").click(function (event) {
        event.preventDefault();
        var url = '/jump.php?action=go&url=' + escape($(this).attr('href'));
        if ($(this).attr('target') == '_blank') {
            window.open(url);
        } else {
            location.href = url;
        }
    });
});
