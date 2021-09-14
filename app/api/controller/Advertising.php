<?php
/**
 * 广告控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\api\controller;

use think\facade\Db;
use think\facade\Request;

class Advertising
{
    /**
     * 广告列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据，只接收数组中的参数
            $data = Request::only(['keywords', 'per_page', 'current_page', 'type']);
            if (empty($data['type'])) {
                //查询所有用户
                $info = Db::name('advertising')
                    ->whereLike('name', "%" . $data['keywords'] . "%")
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            } else {
                //查询所有用户
                $info = Db::name('advertising')
                    ->whereLike('name', "%" . $data['keywords'] . "%")
                    ->order('create_time', 'desc')
                    ->where('type', $data['type'])
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
     * 根据ID查询广告信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        // 接收ID
        $id = Request::param('id');
        // 查询公告信息
        $info = Db::name('advertising')->where('id', $id)->find();
        result(200, "获取数据成功！", $info);
    }
}
