<?php
if (isset($_GET['check']) && $_GET['check'] == 'xdebug')  {
    $address = '192.168.56.101';
    $port = 9000;
    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_bind($sock, $address, $port) or die('Unable to bind');
    socket_listen($sock);
    $client = socket_accept($sock);
    echo "connection established: $client";
    socket_close($client);
    socket_close($sock);
} else {
    phpinfo();
}
