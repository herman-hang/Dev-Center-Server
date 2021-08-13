<?php
/**
 * 应用中心控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\admin\controller;
use think\facade\Db;
use think\facade\Request;
use app\admin\model\App as AppModel;
use app\admin\validate\App as AppValidate;

class App extends Base
{
    /**
     * 应用列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()){
            if (request()->isGet()) {
                // 接收数据
                $data = Request::only(['keywords', 'per_page', 'current_page','type']);
                // 查询所有升级包信息
                $info = Db::name('app')
                    ->whereLike('name|author', "%" . $data['keywords'] . "%")
                    ->where('type',$data['type'])
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

    /**
     * 发布应用
     * @throws \think\db\exception\DbException
     */
    public function add()
    {
        if (request()){
            // 接收数据
            $data = Request::param();
            // 验证数据
            $validate = new AppValidate();
            if (!$validate->sceneAdd()->check($data)){
                result(403,$validate->getError());
            }
            // 执行添加
            $res = AppModel::create($data);
            if ($res){
                $this->log("发布了应用{$data['name']}");
                result(200,"发布成功！");
            }else{
                $this->log("发布应用{$data['name']}失败！");
                result(403,"发布失败！");
            }
        }
    }

    /**
     * 编辑应用
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
            $validate = new AppValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 执行更新
            $app = AppModel::find($data['id']);
            $res = $app->save($data);
            if ($res) {
                $this->log("修改了应用[ID：{$data['id']}]");
                result(200, "修改成功！");
            } else {
                $this->log("修改应用[ID：{$data['id']}]失败！");
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 删除应用
     * @throws \think\db\exception\DbException
     */
    public function delete()
    {
        if (request()->isDelete()) {
            $id = Request::param('id');
            //转为数组
            $array = explode(',', $id);
            // 删除数组中空元素
            $array = array_filter($array);
            // 删除操作
            $res = Db::name('app')->delete($array);
            // 转为字符串
            $array = implode(',', $array);
            if ($res) {
                $this->log("删除了应用[ID：$array}]");
                result(200, "删除成功！");
            } else {
                $this->log("删除应用[ID：{$array}]失败！");
                result(200, "删除失败！");
            }
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


    /**
     * 修改应用状态
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
            $app = AppModel::find($data['id']);
            $res = $app->save($data);
            if ($res) {
                $this->log("修改了应用[id:{$data['id']}]的状态！");
                result(200, "修改成功！");
            } else {
                $this->log("修改应用[id:{$data['id']}]的状态失败！");
                result(403, "修改失败！");
            }
        }
    }
}
