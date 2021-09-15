<?php
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\Request;

class Index extends Base
{
    public function index()
    {
        return redirect(Request::domain(). '/index');
    }

    /**
     * 退出登录
     * @throws \think\db\exception\DbException
     */
    public function loginOut()
    {
        if (request()->isPost()) {
            // 获取当前客户端IP地址
            $ip = Request::ip();
            // 更新
            $res = Db::name('user')->where('id', request()->uid)->update(['lastlog_time' => time(), 'lastlog_ip' => $ip]);
            if ($res) {
                result(200, "退出成功！");
            } else {
                result(403, "退出失败！");
            }
        }
    }
}
