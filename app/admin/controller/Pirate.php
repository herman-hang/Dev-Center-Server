<?php
/**
 * 盗版记录控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;

class Pirate extends Base
{
    /**
     * 盗版记录列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()){
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有开发者
            $info = Db::name('pirate')
                ->whereLike('domain|ip', "%" . $data['keywords'] . "%")
                ->order('create_time', 'desc')
                ->paginate([
                    'list_rows' => $data['per_page'],
                    'query' => request()->param(),
                    'var_page' => 'page',
                    'page' => $data['current_page']
                ]);
            result(200, "获取数据成功！", $info);
        }
    }
}
