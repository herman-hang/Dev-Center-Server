<?php
/**
 * 开发者管理控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\model\User as UserModel;
use app\admin\validate\Developer as DeveloperValidate;

class Developer extends Base
{
    /**
     * 开发者列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有开发者
            $info = Db::view('user', 'id,user,nickname,mobile,email,qq,money')
                ->view('user_developer', 'id,level,brokerage', 'user_developer.user_id=user.id')
                ->whereLike('nickname|user|mobile|email', "%" . $data['keywords'] . "%")
                ->where('is_developer', '2')
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
     * 编辑开发者
     * @throws \think\db\exception\DbException
     */
    public function edit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::except(['user_id', 'user']);
            $validate = new DeveloperValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 更新数据
            $res = Db::name('user_developer')->where('id', $data['id'])->update($data);
            if ($res) {
                $this->log("修改了开发者[ID：{$data['id']}]的信息！");
                result(200, "修改成功！");
            } else {
                $this->log("修改开发者[ID：{$data['id']}]的信息失败！");
                result(403, "修改失败！");
            }
        }
    }

    /**
     * 根据ID查询开发者的信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        $id = Request::param('id');
        // 查询当前开发者的信息
        $info = Db::view('user', 'id,user')
            ->view('user_developer', '*', 'user_developer.user_id=user.id')
            ->where('user_developer.id', $id)
            ->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 开发者降为用户
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function demote()
    {
        if (request()->isPut()) {
            // 接收开发者ID
            $id = Request::param('id');
            // 更新
            $info = Db::name('user_developer')->where('id', $id)->field('user_id')->find();
            $res = Db::name('user')->where('id', $info['user_id'])->update(['is_developer' => '0']);
            if ($res) {
                $this->log("开发者[ID：{$id}]已降为用户！");
                result(200, "操作成功！");
            } else {
                $this->log("开发者[ID：{$id}]降为用户失败！");
                result(403, "操作失败！");
            }
        }
    }

    /**
     * 开发者审核列表
     * @throws \think\db\exception\DbException
     */
    public function auditList()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有开发者
            $info = Db::name('user')
                ->whereLike('nickname|user|mobile|email', "%" . $data['keywords'] . "%")
                ->withoutField(['wx_openid', 'qq_openid', 'weibo_openid', 'status'])
                ->where('is_developer', 'in', '1,3')
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
     * 开发者审核通过操作
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pass()
    {
        if (request()->isPut()) {
            // 接收用户ID
            $id = Request::param('id');
            // 成为开发者
            $result = Db::name('user')->where('id', $id)->update(['is_developer' => '2', 'cause' => NULL]);
            // 判断开发者数据表是否存在该用户的信息
            if ($result) {
                $developer = Db::name('user_developer')->where('user_id', $id)->find();
                // 为空则在数据表中添加一条开发者数据
                if (empty($developer)) {
                    $res = Db::name('user_developer')->insert(['user_id' => $id, 'level' => '0']);
                    if (!$res) {
                        $this->log("用户[ID：{$id}]成为开发者失败！");
                        result(403, "操作失败！");
                    }
                }
                // 构造信息，发布通知邮件
                $info = UserModel::find($id);
                $system = Db::name('system')->where('id', '1')->field('name')->find();
                $title = "恭喜您，审核通过！";
                // 邮件内容
                $content = "<h3>打造生态，我们与您同在！</h3>您在 <strong>{$system['name']}</strong> {$info['create_time']}申请成为开发者的请求已经审核通过，恭喜您成为我们开发团队的一员！";
                // 发送通知邮件
                if (!empty($info['email'])) {
                    $this->sendEmail($info['email'], $title, $content, $info['user']);
                }
                $this->log("用户[ID：{$id}]已成为开发者！");
                result(200, "已通过！");
            } else {
                $this->log("用户[ID：{$id}]成为开发者失败！");
                result(403, "操作失败！");
            }
        }
    }

    /**
     * 申请开发者驳回操作
     * @throws \think\db\exception\DbException
     */
    public function reject()
    {
        if (request()->isPut()) {
            // 接收用户数据
            $data = Request::only(['id', 'cause']);
            // 更新操作
            $res = Db::name('user')->where('id', $data['id'])->update(['is_developer' => '3', 'cause' => $data['cause']]);
            if ($res) {
                // 构造信息，发布通知邮件
                $info = UserModel::find($data['id']);
                $system = Db::name('system')->where('id', '1')->field('name')->find();
                $title = "很遗憾，审核失败！";
                // 邮件内容
                $content = "抱歉！您在 <strong>{$system['name']}</strong> {$info['create_time']}申请成为开发者的请求已经被我们拒绝，请登录{$system['name']}按驳回原因修改后再次提交申请审核！";
                // 发送通知邮件
                if (!empty($info['email'])) {
                    $this->sendEmail($info['email'], $title, $content, $info['user']);
                }
                $this->log("已驳回用户[ID：{$data['id']}]成为开发者请求！");
                result(200, "驳回成功！");
            } else {
                $this->log("已驳回用户[ID：{$data['id']}]成为开发者请求失败！");
                result(403, "驳回失败！");
            }
        }
    }

    /**
     * 获取开发者配置信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function developerConfig()
    {
        if (request()->isGet()) {
            // 查询开发者配置信息
            $info = Db::name('developer_config')->where('id', 1)->find();
            result(200, "获取配置信息成功！", $info);
        }
    }

    /**
     * 编辑开发者配置信息
     * @throws \think\db\exception\DbException
     */
    public function developerConfigEdit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::param();
            // 验证数据
            $validate = new DeveloperValidate();
            if (!$validate->sceneDeveloperConfigEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 更新
            $res = Db::name('developer_config')->where('id', 1)->update($data);
            if ($res) {
                result(200, "修改成功！");
            } else {
                result(403, "修改失败！");
            }
        }
    }
}
