<?php

$m = new Memcached();
$m->addServer('memcached', 11211);

$keys = json_decode(file_get_contents('../keys.txt'), true);

if (isset($_POST['oper'])) {
    if (in_array($_POST['key'], $keys)) {
        if (!$m->get('sessionIndex')) {
            $m->set('sessionIndex', []);
        }
        $sessionIndex = $m->get('sessionIndex');
        if (!in_array($_POST['session'], $sessionIndex)) {
            $sessionIndex[] = $_POST['session'];
            $m->set('sessionIndex', $sessionIndex);
        }
        $m->set($_POST['session'], ';' . $_POST['communityname'] . ';' . $_POST['communitydiscord'] . ';' . $_POST['communitylogo'] . ';' . $_POST['sessionpreview'] . ';' . $_POST['sessionname'] . ';' . $_POST['sessionhost'] . ';' . $_POST['usercount'] . ';' . $_POST['userlist'], time() + 120);
    } else {
        header("HTTP/1.1 401 Unauthorized");
        exit;
    }
} else {
    $sessionIndex = $m->get('sessionIndex');
    if (is_array($sessionIndex)) {
        foreach($sessionIndex as $item) {
            if ($m->get($item)) {
                echo $item . $m->get($item) . '|';
            } else {
                $updatedIndex = array_diff($sessionIndex, [$item]);
                $m->set('sessionIndex', $updatedIndex);
            }
        }
    } else {
        exit();
    }
}
?>
