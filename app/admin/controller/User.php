<?php
/**
 * 用户控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\validate\User as UserValidate;
use app\admin\model\User as UserModel;

class User extends Base
{
    /**
     * 用户列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有用户
            $info = Db::name('user')
                ->whereLike('nickname|user|mobile|email', "%" . $data['keywords'] . "%")
                ->withoutField(['wx_openid', 'qq_openid', 'weibo_openid'])
                ->where('is_developer', '0')
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
     * 添加用户
     * @throws \think\db\exception\DbException
     */
    public function add()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::except(['create_time', 'update_time', 'wx_openid', 'qq_openid', 'weibo_openid']);
            // 验证数据
            $validate = new UserValidate();
            if (!$validate->sceneAdd()->check($data)) {
                result(403, $validate->getError());
            }
            //对密码进行加密
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            // 执行添加
            $res = UserModel::create($data);
            if ($res) {
                $this->log("添加用户{$data['user']}成功！");
                result(201, "添加成功！");
            } else {
                $this->log("添加用户{$data['user']}失败！");
                result(403, "添加失败！");
            }
        }
    }

    /**
     * 编辑用户
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::except(['create_time', 'update_time', 'status', 'wx_openid', 'qq_openid', 'weibo_openid']);
            // 验证数据
            $validate = new UserValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 查询当前管理员的密码
            $info = Db::name('user')->where('id', $data['id'])->field('user,password')->find();
            // 执行更新
            $user = UserModel::find($data['id']);
            // 判断密码是否已经修改
            if ($data['password'] !== $info['password']) {
                // 重新hash加密
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
            // 判断是否已经设置了开发者，设置了则判断开发者数据表是否存在数据，不存在则添加
            if ($data['is_developer'] == '2') {
                $developer = Db::name('user_developer')->where('user_id', $data['id'])->find();
                if (empty($developer)) {
                    // 插入一条开发者数据
                    $result = Db::name('user_developer')->insert(['user_id' => $data['id'], 'level' => '0']);
                    if (!$result) {
                        $this->log("用户[ID：{$data['id']}]成为开发者失败！");
                        result(403, "修改失败！");
                    }
                }
            }
            $res = $user->save($data);
            if ($res) {
                $this->log("修改用户{$info['user']}信息成功！");
                result(200, "修改成功！");
            } else {
                $this->log("修改用户{$info['user']}信息失败！");
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 根据ID获取用户信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        // 接收用户ID
        $id = Request::param('id');
        // 查询用户信息
        $info = Db::name('user')->withoutField(['wx_openid', 'qq_openid', 'weibo_openid'])->where('id', $id)->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 删除用户
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
            $res = Db::name('user')->delete($array);
            // 转为字符串
            $array = implode(',', $array);
            if ($res) {
                $this->log("删除了用户[ID：{$array}]");
                result(200, "删除成功！");
            } else {
                $this->log("删除用户[ID：{$array}]失败！");
                result(200, "删除失败！");
            }
        }
    }

    /**
     * 修改用户的状态状态
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
            $user = UserModel::find($data['id']);
            $res = $user->save($data);
            if ($res) {
                $this->log("修改了用户[id:{$data['id']}]的状态！");
                result(200, "修改成功！");
            } else {
                $this->log("修改用户[id:{$data['id']}]的状态失败！");
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 用户消费明细
     * @throws \think\db\exception\DbException
     */
    public function buyLog()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有用户
            $info = Db::name('user_buylog')
                ->whereLike('indent|uid', "%" . $data['keywords'] . "%")
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
