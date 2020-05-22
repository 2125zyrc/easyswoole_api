<?php

namespace EasySwoole\HotReload;

use Swoole\Server;

/**
 * 热重载管理器
 * Class HotReloadProcess
 * @package EasySwoole\HotReload
 */
class HotReload
{
    protected $hotReloadOptions;
    protected $hotReloadProcess;

    function __construct(HotReloadOptions $hotReloadOptions)
    {
        $this->hotReloadOptions = $hotReloadOptions;
    }

    /**
     * 附加到当前服务
     * @param Server $server
     */
    function attachToServer(Server $server)
    {
        $this->hotReloadProcess = new HotReloadProcess;
        $this->hotReloadProcess->setSwooleServer($server);
        $this->hotReloadProcess->setHotReloadOptions($this->hotReloadOptions);

        $server->addProcess($this->hotReloadProcess->getProcess());
    }
}