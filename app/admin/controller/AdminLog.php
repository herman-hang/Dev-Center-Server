<?php
/**
 * 日志记录控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;

class AdminLog extends Base
{
    /**
     * 日志记录列表
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page','type']);
            //关联查询
            $info = Db::view('admin_log')
                ->view('admin', 'user', 'admin.id=admin_log.admin_id')
                ->where('admin_log.admin_id', request()->uid)
                ->where('type', $data['type'])
                ->order('create_time', 'desc')
                ->paginate([
                    'list_rows' => $data['per_page'],
                    'query' => request()->param(),
                    'var_page' => 'page',
                    'page' => $data['current_page']
                ]);
            result(200,"获取日志数据成功！",$info);
        }
    }
}
