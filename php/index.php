<?php

$m = new Memcached();
$m->addServer('localhost', 11211);

if (isset($_POST['oper'])) {
    if (in_array($_POST['key'], array('ACCESSKEY1', 'ACCESSKEY2'))) {
        $m->set($_POST['session'], ';' . $_POST['communityname'] . ';' . $_POST['communitydiscord'] . ';' . $_POST['communitylogo'] . ';' . $_POST['sessionpreview'] . ';' . $_POST['sessionname'] . ';' . $_POST['sessionhost'] . ';' . $_POST['usercount'] . ';' . $_POST['userlist'], time() + 120);
    } else {
        header("HTTP/1.1 401 Unauthorized");
        exit;
    }
} else {
    $keys = $m->getAllKeys();
    foreach($keys as $item) {
        echo $item . $m->get($item) . '|';
    }
}

?>
