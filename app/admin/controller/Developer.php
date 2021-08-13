<?php
/**
 * 开发者管理控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;

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
            $info = Db::view('user', 'id,user,nickname,mobile,email,qq')
                ->view('user_developer', 'id,level', 'user_developer.user_id=user.id')
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
            $data = Request::except(['user_id']);
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
        $info = Db::name('user_developer')->where('id', $id)->withoutField('user_id,brokerage')->find();
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
            // 获取开发者ID
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
                ->withoutField(['wx_openid', 'qq_openid', 'weibo_openid'])
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
            $result = Db::name('user')->where('id', $id)->update(['is_developer' => 2]);
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
                $this->log("已驳回用户[ID：{$data['id']}]成为开发者请求！");
                result(200, "驳回成功！");
            } else {
                $this->log("已驳回用户[ID：{$data['id']}]成为开发者请求失败！");
                result(403, "驳回失败！");
            }
        }
    }
}
