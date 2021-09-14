<?php
/**
 * 授权控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\api\controller;

use think\facade\Db;
use think\facade\Request;

class Authorization extends Base
{
    /**
     * 检测域名是否授权
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkAuth()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::only(['domain']);
            // 查询该域名是否授权
            $res = Db::name('authorization')->where('user_id', request()->uid)->where('domain_one|domain_two|domain_tree', $data['domain'])->field('ip')->find();
            if (empty($res)) {
                // 记录盗版
                Db::name('pirate')->insert(['domain' => $data['domain'], 'ip' => Request::ip(), 'create_time' => time()]);
                result(403, "未授权");
            }
            // 检测IP地址
            if ($res['ip'] !== Request::ip()) {
                result(403, "授权IP地址错误，请及时更新授权信息！");
            }
        }
    }
}
