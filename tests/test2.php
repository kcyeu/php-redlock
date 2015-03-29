<?php

require_once __DIR__ . '/../RedLock/RedLock.php';

//define('USE_REDIS_CLUSTER', TRUE);
define('USE_REDIS_CLUSTER', FALSE);
define('LOCK_TIMEOUT', 10);

if (USE_REDIS_CLUSTER):
    $servers = array(
        array('172.16.10.168', 6379),
        array('172.16.10.193', 6379),
        array('172.16.10.169', 6379)            
    );
else:
    $servers = array(
        array('127.0.0.1', 6379),
    );
endif;

$redLock = new RedLock($servers);

while (true)
{
    $lock = $redLock->lock('test', LOCK_TIMEOUT);

    if ($lock)
    {
        print_r($lock);

        // Do something here
        $sleep = LOCK_TIMEOUT / 2;
        echo "Unlocking in {$sleep} seconds\n";
        sleep($sleep);

        $redLock->unlock($lock);
    }
    else
    {
        print "Lock not acquired\n";
    }

    // Buffer before next attempt
    usleep((time() % 11) * 100000);
}
