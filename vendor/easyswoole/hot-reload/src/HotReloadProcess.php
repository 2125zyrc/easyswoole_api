<?php

namespace EasySwoole\HotReload;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\HotReload\Monitor\FileScanner;
use EasySwoole\HotReload\Monitor\Inotify;
use Swoole\Server;

/**
 * 热重载处理进程
 * Class HotReloadProcess
 * @package EasySwoole\HotReload
 */
class HotReloadProcess extends AbstractProcess
{
    /**
     * 热重载配置
     * @var HotReloadOptions
     */
    protected $hotReloadOptions;

    /**
     * 当前的主服务
     * @var Server
     */
    protected $swooleServer;

    /**
     * 热重载监控进程
     * @param $arg
     */
    protected function run($arg)
    {
        // 注册SIGUSR1 收到该信号执行重载逻辑
        $this->getProcess()->signal(SIGUSR1, function () {
            $this->onReloadSignal();
        });

        $currentOS = PHP_OS;
        $currentPID = $this->getProcess()->pid;
        $reloadMonitor = $this->selectMonitor();
        $reloadMonitor->startMonitor();
        echo "{$reloadMonitor->monitorName()} hot reload initialize at {$currentOS} in PID {$currentPID} ...\n";
    }

    /**
     * 获得一个监视器实例
     * @return FileScanner|Inotify
     */
    protected function selectMonitor()
    {
        if (extension_loaded('inotify') && !$this->hotReloadOptions->isDisableInotify()) {
            return new Inotify($this->hotReloadOptions, $this->getProcess());
        }
        return new FileScanner($this->hotReloadOptions, $this->getProcess());
    }

    /**
     * HotReloadOptions Setter
     * @param mixed $hotReloadOptions
     */
    public function setHotReloadOptions($hotReloadOptions)
    {
        $this->hotReloadOptions = $hotReloadOptions;
    }

    /**
     * SwooleServer Setter
     * @param Server $swooleServer
     */
    public function setSwooleServer($swooleServer)
    {
        $this->swooleServer = $swooleServer;
    }

    /**
     * 收到重载信号时
     * 如果当前定义了回调则执行用户回调
     */
    public function onReloadSignal()
    {
        $reloadCallback = $this->hotReloadOptions->getReloadCallback();
        if (is_callable($reloadCallback)) {
            $reloadCallback($this->swooleServer);
            return;
        }

        echo "HOT_RELOAD: reloaded at " . time() . "\n";
        $this->swooleServer->reload();
    }
}