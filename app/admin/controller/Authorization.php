<?php
/**
 * 授权站点控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\validate\Authorization as AuthorizationValidate;
use app\admin\model\Authorization as AuthorizationModel;

class Authorization extends Base
{
    /**
     * 授权站列表
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
     * 添加授权站点
     * @throws \think\db\exception\DbException
     */
    public function add()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::except(['create_time', 'update_time']);
            // 验证数据
            $validate = new AuthorizationValidate();
            if (!$validate->sceneAdd()->check($data)) {
                result(403, $validate->getError());
            }
            // 执行添加
            $res = AuthorizationModel::create($data);
            if ($res) {
                $this->log("添加了授权站点{$data['name']}");
                result(201, "添加成功！");
            } else {
                $this->log("添加授权站点{$data['name']}失败！");
                result(403, "添加失败！");
            }
        }
    }

    /**
     * 修改授权站点
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::except(['create_time', 'update_time', 'status']);
            // 验证数据
            // 验证数据
            $validate = new AuthorizationValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 执行更新操作
            $authorization = AuthorizationModel::find($data['id']);
            $res = $authorization->save($data);
            if ($res) {
                $this->log("修改了授权站点{$data['name']}");
                result(200, "修改成功！");
            } else {
                $this->log("修改授权站点{$data['name']}失败！");
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 删除授权站点
     * @throws \think\db\exception\DbException
     */
    public function delete()
    {
        if (request()->isDelete()) {
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
            $res = Db::name('authorization')->delete($array);
            // 转为字符串
            $array = implode(',', $array);
            if ($res) {
                $this->log("删除了授权站点[ID：{$array}]");
                result(200, "删除成功！");
            } else {
                $this->log("删除授权站点[ID：{$array}]失败！");
                result(200, "删除失败！");
            }
        }
    }

    /**
     * 根据ID查询授权站点的信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        if (request()->isGet()) {
            // 接收授权站点ID
            $id = Request::param('id');
            // 查询用户信息
            $info = Db::name('authorization')->where('id', $id)->find();
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 修改授权站点的状态
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function statusEdit()
    {
        if (request()->isPut()) {
            // 接收ID
            $data = Request::only(['id', 'status']);
            // 执行更新
            $authorization = AuthorizationModel::find($data['id']);
            $res = $authorization->save($data);
            if ($res) {
                $this->log("修改了授权站点[id:{$data['id']}]的状态！");
                result(200, "修改成功！");
            } else {
                $this->log("修改授权站点[id:{$data['id']}]的状态失败！");
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 授权配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function authConfig()
    {
        if (request()->isGet()) {
            $info = Db::name('authorization_config')->where('id', 1)->find();
            result(200, "数据获取成功！", $info);
        }
    }

    /**
     * 编辑授权配置信息
     * @throws \think\db\exception\DbException
     */
    public function authConfigEdit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::param();
            // 更新
            $res = Db::name('authorization_config')->where('id', 1)->update($data);
            if ($res) {
                $this->log("修改了授权配置信息！");
                result(200, "修改成功！");
            } else {
                $this->log("修改授权配置信息失败！");
                result(403, "修改失败！");
            }
        }
    }
}
