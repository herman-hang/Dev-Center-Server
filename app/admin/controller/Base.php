<?php
/**
 * 模块公共控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Request;
use app\admin\model\AdminLog;

class Base extends BaseController
{
    /**
     * 记录管理员日志
     * @param string $content 日志内容
     * @param int $type 日志类型（1为登录日志，2为操作日志）
     * @param null $id 管理员ID
     * @throws \think\db\exception\DbException
     */
    public function log(string $content, int $type = 2, $id = null)
    {
        //删除大于60天的日志
        Db::name('admin_log')->where('create_time', '<= time', time() - (84600 * 60))->delete();
        //记录当前客户端IP地址
        $ip = Request::ip();
        //实例化对象
        $log = new AdminLog();
        //执行添加并过滤非数据表字段
        $log->save(['type' => $type, 'admin_id' => $id, 'content' => $content, 'ip' => $ip]);
    }
}
