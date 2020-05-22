<?php
/**
 * @CreateTime:   2020-03-25 23:55
 * @Author:       huizhang  <2788828128@qq.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  编码工具
 */
namespace EasySwoole\Jwt;

use EasySwoole\Component\Singleton;

class Encryption
{
    use Singleton;

    public function base64UrlEncode($content)
    {
        return str_replace('=', '', strtr(base64_encode($content), '+/', '-_'));
    }

    public function base64UrlDecode($content)
    {
        $remainder = strlen($content) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $content .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($content, '-_', '+/'));
    }

}