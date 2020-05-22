<?php


namespace EasySwoole\HotReload\Monitor;

/**
 * Inotify监视器
 * Class Inotify
 * @package EasySwoole\HotReload\Monitor
 */
class Inotify extends AbstractMonitor
{
    /**
     * 获取监视器名称
     * @return string
     */
    public function monitorName()
    {
        return 'Inotify';
    }

    /**
     * 启动当前监视器
     * @return void
     */
    public function startMonitor()
    {
        // 因为进程独立 且当前是自定义进程 全局变量只有该进程使用
        // 在确定不会造成污染的情况下 也可以合理使用全局变量
        global $lastReloadTime;
        global $inotifyResource;

        $lastReloadTime = 0;
        $fileList = $this->monitoredList();
        if (!empty($fileList)) {

            $inotifyResource = inotify_init();

            // 为设置的目录增加监控
            foreach ($fileList as $item) {
                inotify_add_watch($inotifyResource, $item, IN_CREATE | IN_DELETE | IN_MODIFY);
            }

            // 加入事件循环
            swoole_event_add($inotifyResource, function () {
                global $lastReloadTime;
                global $inotifyResource;
                $events = inotify_read($inotifyResource);
                if ($lastReloadTime < time() && !empty($events)) { // 限制1s内不能进行重复reload
                    $lastReloadTime = time();
                    $this->sendReloadSignal();  // 向重载进程发送信号通知重载
                }
            });
        } else {
            echo "WARNING: HotReload no files to monitor\n";
        }
    }
}