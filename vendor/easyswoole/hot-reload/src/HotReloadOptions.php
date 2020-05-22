<?php

namespace EasySwoole\HotReload;

use EasySwoole\Spl\SplBean;

/**
 * 热重载选项
 * Class HotReloadOptions
 * @package EasySwoole\HotReload
 */
class HotReloadOptions extends SplBean
{
    protected $monitorFolder = [];
    protected $ignoreSuffix = [];
    protected $disableInotify = false;
    protected $reloadCallback = null;

    /**
     * MonitorFolder Getter
     * @return array
     */
    public function getMonitorFolder()
    {
        return $this->monitorFolder;
    }

    /**
     * MonitorFolder Setter
     * @param array $monitorFolder
     * @return HotReloadOptions
     */
    public function setMonitorFolder($monitorFolder)
    {
        $this->monitorFolder = $monitorFolder;
        return $this;
    }

    /**
     * DisableInotify Getter
     * @return bool
     */
    public function isDisableInotify()
    {
        return $this->disableInotify;
    }

    /**
     * DisableInotify Setter
     * @param bool $disableInotify
     * @return HotReloadOptions
     */
    public function disableInotify($disableInotify = true)
    {
        $this->disableInotify = $disableInotify;
        return $this;
    }

    /**
     * ReloadCallback Getter
     * @return null
     */
    public function getReloadCallback()
    {
        return $this->reloadCallback;
    }

    /**
     * ReloadCallback Setter
     * @param callable $reloadCallback
     * @return HotReloadOptions
     */
    public function setReloadCallback(callable $reloadCallback)
    {
        $this->reloadCallback = $reloadCallback;
        return $this;
    }

    /**
     * IgnoreSuffix Getter
     * @return array
     */
    public function getIgnoreSuffix()
    {
        return $this->ignoreSuffix;
    }

    /**
     * IgnoreSuffix Setter
     * @param array $ignoreSuffix
     * @return HotReloadOptions
     */
    public function setIgnoreSuffix($ignoreSuffix)
    {
        $this->ignoreSuffix = $ignoreSuffix;
        return $this;
    }

}