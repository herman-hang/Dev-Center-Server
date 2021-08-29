<?php
/**
 * 盗版记录控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use Yurun\Util\HttpRequest;

class Pirate extends Base
{
    /**
     * 盗版记录列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
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

    public function go()
    {
        if (request()->isPost()) {
            // 接收ID
            $id = Request::param('id');
            // 查询盗版信息
            $info = Db::name('pirate')->where('id', $id)->find();
            // 发起请求
            $http = new HttpRequest();
            $url = "http://layui.muyucms.com/muyu.php/base/go";
            $response = $http->ua('YurunHttp')->post($url, ['key' => '123456'])->json();
            result(200,"获取数据成功！",$response);
        }
    }
}
