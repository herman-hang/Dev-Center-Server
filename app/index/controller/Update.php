<?php
/**
 * 更新系统版本控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use think\facade\Request;
use think\facade\Db;

class Update extends Base
{
    /**
     * 检测版本号信息
     */
    public function checkVersion()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::only(['version']);
            // 获取所有版本号，从小到大排列
            $versionArray = Db::name('upgrade')->where('status', '1')->order('create_time', 'asc')->column('version');
            // 获取当前版本的下一个版本号
            $key = array_search($data['version'], $versionArray);
            if (!empty($versionArray[$key + 1])) {
                $info = Db::name('upgrade')->where('version', $versionArray[$key + 1])->field('content,type,version,wgt_url,way')->find();
                if ($info) {
                    result(200, "获取最新版本成功！", $info);
                } else {
                    result(403, "获取最新版本失败！");
                }
            } else {
                result(403, "当前版本已是最新！");
            }
        }
    }
}
