<?php
/**
 * 广告控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\model\Advertising as AdvertisingModel;
use app\admin\validate\Advertising as AdvertisingValidate;

class Advertising extends Base
{
    /**
     * 广告列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据，只接收数组中的参数
            $data = Request::only(['keywords', 'per_page', 'current_page']);
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
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 发布广告
     * @throws \think\db\exception\DbException
     */
    public function add()
    {
        // 接收数据，同时过滤数组中的参数
        $data = Request::except(['create_time', 'update_time']);
        // 验证数据
        $validate = new AdvertisingValidate();
        if (!$validate->sceneAdd()->check($data)) {
            result(403, $validate->getError());
        }
        // 执行添加
        $res = AdvertisingModel::create($data);
        if ($res) {
            $this->log("发布了广告：{$data['name']}");
            result(201, "发布成功！");
        } else {
            $this->log("发布广告：{$data['name']}失败！");
            result(403, "发布失败！");
        }
    }


    /**
     * 编辑广告
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        if (request()->isPut()) {
            // 接收数据，同时过滤数组中的参数
            $data = Request::except(['status', 'create_time', 'update_time']);
            // 验证数据
            $validate = new AdvertisingValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 执行更新
            $advertising = AdvertisingModel::find($data['id']);
            $res = $advertising->save($data);
            if ($res) {
                $this->log("修改了广告{$data['name']}的信息！");
                result(200, "修改成功！");
            } else {
                $this->log("修改广告{$data['name']}信息失败！");
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 删除广告
     * @throws \think\db\exception\DbException
     */
    public function delete()
    {
        if (request()->isDelete()) {
            // 接收ID
            $id = Request::param('id');
            if (!strpos($id, ',')) {
                $array = array($id);
            } else {
                //转为数组
                $array = explode(',', $id);
                // 删除数组中空元素
                $array = array_filter($array);
            }
            // 删除操作
            $res = Db::name('advertising')->delete($array);
            // 转为字符串
            $array = implode(',', $array);
            if ($res) {
                $this->log("删除了广告[ID：{$array}]");
                result(200, "删除成功！");
            } else {
                $this->log("删除广告[ID：{$array}]失败！");
                result(403, "删除失败！");
            }
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


    /**
     * 编辑广告状态
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function statusEdit()
    {
        if (request()->isPut()) {
            // 接收数据，只接收数组中的参数
            $data = Request::only(['id', 'status']);
            // 执行更新
            $advertising = AdvertisingModel::find($data['id']);
            $res = $advertising->save($data);
            if ($res) {
                $this->log("修改了广告[id:{$data['id']}]的状态！");
                result(200, "修改成功！");
            } else {
                $this->log("修改广告[id:{$data['id']}]的状态失败！");
                result(403, "修改失败！");
            }
        }
    }
}
