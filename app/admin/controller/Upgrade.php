<?php
/**
 * 升级中心控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\validate\Upgrade as UpgradeValidate;
use app\admin\model\Upgrade as UpgradeModel;

class Upgrade extends Base
{
    /**
     * 版本发布列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            // 查询所有升级包信息
            $info = Db::name('upgrade')
                ->whereLike('title|version', "%" . $data['keywords'] . "%")
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
     * 发布升级包
     * @throws \think\db\exception\DbException
     */
    public function add()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::param();
            // 验证数据
            $validate = new UpgradeValidate();
            if (!$validate->sceneAdd()->check($data)) {
                result(403, $validate->getError());
            }
            // 发布操作
            $res = UpgradeModel::create($data);
            if ($res) {
                $this->log("发布了升级包[version：{$data['version']}]");
                result(201, "发布成功！");
            } else {
                $this->log("发布升级包[version：{$data['version']}]失败！");
                result(403, "发布失败！");
            }
        }
    }

    /**
     * 编辑升级包
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::except(['status', 'create_time', 'update_time']);
            // 验证数据
            $validate = new UpgradeValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 执行更新
            $upgrade = UpgradeModel::find($data['id']);
            $res = $upgrade->save($data);
            if ($res) {
                $this->log("修改了升级包[version：{$data['version']}]");
                result(200, "修改成功！");
            } else {
                $this->log("修改升级包[version：{$data['version']}]失败！");
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 删除升级包
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
            $res = Db::name('upgrade')->delete($array);
            // 转为字符串
            $array = implode(',', $array);
            if ($res) {
                $this->log("删除了升级包[ID：$array}]");
                result(200, "删除成功！");
            } else {
                $this->log("删除升级包[ID：{$array}]失败！");
                result(200, "删除失败！");
            }
        }
    }

    /**
     * 根据ID查询升级包的信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        // 接收升级包ID
        $id = Request::param('id');
        // 查询用户信息
        $info = Db::name('upgrade')->where('id', $id)->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 修改升级包状态
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function statusEdit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::only(['id', 'status']);
            // 执行更新
            $upgrade = UpgradeModel::find($data['id']);
            $res = $upgrade->save($data);
            if ($res) {
                $this->log("修改了升级包[id:{$data['id']}]的状态！");
                result(200, "修改成功！");
            } else {
                $this->log("修改升级包[id:{$data['id']}]的状态失败！");
                result(403, "修改失败！");
            }
        }
    }
}
