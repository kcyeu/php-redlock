<?php

namespace RedLock;

class RedLock
{
    const TIMEOUT = 10;
    private $_redis;

    public function __construct($servers)
    {
        if (count($servers) == 1)
        {
            $this->_redis = new \Redis();                                                                                                       
            $this->_redis->connect($servers[0][0], $servers[0][1]);
        }
        else
        {
            $nodes = array();
            foreach ($servers as $server)
            {
                $nodes[] = implode(':', $server);
            }
            $this->_redis = new RedisCluster('cluster1', $nodes);
        }
    }

    public function lock($resource, $timeout = self::TIMEOUT)
    {
        $token = uniqid();

        $res = $this->_redis->set($resource, $token, array('nx', 'ex' => $timeout));

        return ($res) ? array('resource' => $resource, "create time" => time(), "token" => $token) : NULL;
    }

    public function unlock($lock)
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        return $this->_redis->eval($script, [$lock['resource'], $lock['token']], 1);

    }
} 

?>
