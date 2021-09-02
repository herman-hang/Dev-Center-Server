<?php
/**
 * 授权中心控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\Request;

class Authorization extends Base
{
    /**
     * 我的授权列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有开发者
            $info = Db::name('authorization')
                ->whereLike('name|ip|domain_one|domain_two|domain_tree', "%" . $data['keywords'] . "%")
                ->where('user_id', request()->uid)
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

    /**
     * 查询配置信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function queryConfig()
    {
        if (request()->isGet()) {
            $info = Db::name('authorization_config')->where('id', 1)->find();
            result(200, "获取配置信息成功！", $info);
        }
    }

}
