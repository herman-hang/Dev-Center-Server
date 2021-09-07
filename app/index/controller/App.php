<?php
/**
 * 应用发布控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use thans\jwt\facade\JWTAuth;
use think\facade\Db;
use think\facade\Request;
use app\index\model\App as AppModel;
use app\index\validate\App as AppValidate;

class App extends Base
{
    protected function initialize()
    {
        // 验证用户是否为开发者，不是开发者本控制都无法访问
        $expToken = JWTAuth::auth(false);
        $id = $expToken['uid']->getValue();
        // 查询用户信息
        $user = Db::name('user')->where('id', $id)->field('is_developer')->find();
        if ($user['is_developer'] !== "2") {
            result(403, "非开发者无权限访问！");
        }
    }

    /**
     * 应用发布列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page', 'type']);
            if ($data['type'] === "") {
                // 查询所有升级包信息
                $info = Db::name('app')
                    ->whereLike('name|author', "%" . $data['keywords'] . "%")
                    ->where('user_id', request()->uid)
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
                    ->where('user_id', request()->uid)
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
     * 发布应用
     */
    public function add()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::except(['download', 'cause']);
            // 验证数据
            $validate = new AppValidate();
            if (!$validate->sceneAdd()->check($data)) {
                result(403, $validate->getError());
            }
            // 过滤XSS攻击
            $data = $this->removeXSS($data);
            // 状态变更为审核中
            $data['status'] = '1';
            // 用户ID
            $data['user_id'] = request()->uid;
            // 开发者ID
            $developer = Db::name('user_developer')->where('user_id', request()->uid)->field('id')->find();
            $data['developer_id'] = $developer['id'];
            // 执行添加
            $res = AppModel::create($data);
            if ($res) {
                result(201, "发布成功！");
            } else {
                result(403, "发布失败！");
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
            $data = Request::except(['status', 'create_time', 'update_time', 'download', 'cause']);
            // 验证数据
            $validate = new AppValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 过滤XSS攻击
            $data = $this->removeXSS($data);
            // 状态变更为审核中
            $data['status'] = '1';
            // 执行更新
            $app = AppModel::find($data['id']);
            $res = $app->save($data);
            if ($res) {
                result(200, "修改成功！");
            } else {
                result(403, "修改失败！");
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
}
