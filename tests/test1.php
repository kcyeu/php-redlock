<?php

require_once __DIR__ . '/../src/RedLock.php';

if (USE_REDIS_CLUSTER):
    $servers = array(
        array('172.16.10.168', 6379),
        array('172.16.10.193', 6379),
        array('172.16.10.169', 6379)            
    );
else:
    $servers = array(
        array('127.0.0.1', 6379, 0.01),
    );
endif;

$redLock = new RedLock($servers);

while (true) {
    $lock = $redLock->lock('test', 10000);
    if ($lock) {
        print_r($lock);
    } else {
        print "Lock not acquired\n";
    }
}
