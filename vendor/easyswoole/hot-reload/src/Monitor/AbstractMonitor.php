<?php


namespace EasySwoole\HotReload\Monitor;

use EasySwoole\HotReload\HotReloadOptions;
use EasySwoole\Utility\File;
use Swoole\Process;

/**
 * 抽象监视器
 * Class AbstractMonitor
 * @package EasySwoole\HotReload\Monitor
 */
abstract class AbstractMonitor
{
    /**
     * 热重载配置
     * @var HotReloadOptions
     */
    protected $hotReloadOptions;

    /**
     * 热重载进程
     * @var Process
     */
    protected $hotReloadProcess;

    /**
     * AbstractMonitor constructor.
     * @param HotReloadOptions $hotReloadOptions
     * @param Process $hotReloadProcess
     */
    function __construct(HotReloadOptions $hotReloadOptions, Process $hotReloadProcess)
    {
        $this->hotReloadProcess = $hotReloadProcess;
        $this->hotReloadOptions = $hotReloadOptions;
    }

    /**
     * 给当前的重载进程发送服务重载信号
     * 重载进程收到该信号后会对当前挂载的Server执行重启逻辑
     * 如果用户自行注册了重载事件那么需要用户自行实现重载逻辑
     */
    protected function sendReloadSignal()
    {
        Process::kill($this->hotReloadProcess->pid, SIGUSR1);
    }

    /**
     * 取得当前被监控目录的项目列表
     * @return array
     */
    protected function monitoredList()
    {
        $fileList = array();
        foreach ($this->hotReloadOptions->getMonitorFolder() as $folder) {
            if (is_dir($folder)) {
                $files = File::scanDirectory($folder);
                foreach ($files['files'] as $inde => $file) {
                    if (in_array(pathinfo($file, PATHINFO_EXTENSION), $this->hotReloadOptions->getIgnoreSuffix())) {
                        unset($files['files'][$inde]);
                    }
                }
                $fileList = array_merge($files['files'], $files['dirs']);
            }
        }
        return $fileList;
    }

    abstract function monitorName();

    abstract function startMonitor();

}
