<?php

defined("REDIS_HOST") ?: define('REDIS_HOST', '127.0.0.1');
defined("REDIS_PORT") ?: define('REDIS_PORT', 6379);
defined("REDIS_AUTH") ?: define('REDIS_AUTH', 'easyswoole');
defined("REDIS_CLUSTER_SERVER_LIST") ?: define('REDIS_CLUSTER_SERVER_LIST',
    [
        ['127.0.0.1', 9001],
        ['127.0.0.1', 9002],
        ['127.0.0.1', 9003],
        ['127.0.0.1', 9004],
    ]);
defined("REDIS_CLUSTER_AUTH") ?: define('REDIS_CLUSTER_AUTH', '');
