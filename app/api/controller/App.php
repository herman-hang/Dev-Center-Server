<?php
/**
 * 应用中心控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\api\controller;

use think\facade\Db;
use think\facade\Request;

class App
{
    /**
     * 应用列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page', 'type']);
            if (empty($data['type'])) {
                // 查询所有升级包信息
                $info = Db::name('app')
                    ->whereLike('name|author', "%" . $data['keywords'] . "%")
                    ->where('status', 'in', '0,2')
                    ->withoutField('cause')
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            } else {
                // 查询指定类型的应用
                $info = Db::name('app')
                    ->whereLike('name|author', "%" . $data['keywords'] . "%")
                    ->where('type', $data['type'])
                    ->where('status', '2')
                    ->withoutField('cause')
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            }
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 根据ID查询应用信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        // 接收升级包ID
        $id = Request::param('id');
        // 查询用户信息
        $info = Db::name('app')->where('id', $id)->find();
        result(200, "获取数据成功！", $info);
    }
}
