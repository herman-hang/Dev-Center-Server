<?php
/**
 * 管理员控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\validate\Admin as AdminValidate;
use app\admin\model\Admin as AdminModel;

class Admin extends Base
{
    /**
     * 管理员列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            // ID为1的是超级管理员，拥有最高权限，输出全部管理员信息
            if (request()->uid == 1) {
                // 查询所有管理信息
                $list = Db::view('admin', 'id,user,password,photo,name,card,sex,age,region,mobile,email,introduction,create_time,update_time,status,role_id')
                    ->view('group', 'name as rolename', 'admin.role_id=group.id')
                    ->whereLike('admin.name|user|mobile|email', "%" . $data['keywords'] . "%")
                    ->order('admin.create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            } else {
                // 查询当前管理员的信息
                $list = Db::view('admin', 'user,password,photo,name,card,sex,age,region,mobile,email,introduction,create_time,update_time,status,role_id')
                    ->view('group', 'name as rolename', 'admin.role_id=group.id')
                    ->where('admin.id', request()->uid)
                    ->whereLike('admin.name|user|mobile|email', "%" . $data['keywords'] . "%")
                    ->order('admin.create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            }
            result(200, "获取数据成功！", $list->toArray());
        }
    }

    /**
     * 添加管理员
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::only(['user', 'password', 'passwords', 'photo', 'name', 'card', 'sex', 'age', 'region', 'mobile', 'email', 'introduction', 'role_id']);
            // 验证数据
            $validate = new AdminValidate();
            if (!$validate->sceneAdd()->check($data)) {
                result(403, $validate->getError());
            }
            //对密码进行加密
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            //执行添加并过滤非数据表字段
            $res = AdminModel::create($data);
            if ($res) {
                //记录日志
                $this->log("添加了管理员：{$data['user']}");
                result(201, "添加成功！");
            } else {
                //记录日志
                $this->log("添加管理员{$data['user']}失败！");
                result(403, "添加失败！");
            }
        }
    }

    /**
     * 编辑管理员
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        if (request()->isPut()) {
            // 接收数据
            $data = Request::only(['id', 'user', 'password', 'photo', 'name', 'card', 'sex', 'age', 'region', 'mobile', 'email', 'introduction', 'role_id']);
            // 验证数据
            $validate = new AdminValidate();
            if (!$validate->sceneEdit()->check($data)) {
                result(403, $validate->getError());
            }
            // 查询当前管理员的密码
            $info = Db::name('admin')->where('id', $data['id'])->field('user,password')->find();
            // 执行更新
            $admin = AdminModel::find($data['id']);
            //如果为超级管理员，则可以修改密码，否则不行
            if (request()->uid == 1) {
                // 判断密码是否已经修改
                if ($data['password'] !== $info['password']) {
                    // 重新hash加密
                    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                }
                $res = $admin->save($data);
            } else {
                // 普通管理不能修改密码，若存在密码则删除
                if (isset($data['password'])) {
                    // 删除密码
                    unset($data['password']);
                }
                // 执行更新
                $res = $admin->save($data);;
            }
            if ($res) {
                //记录日志
                $this->log("修改了管理员：{$info['user']}的个人信息！");
                result(200, "修改成功！");
            } else {
                //记录日志
                $this->log("修改管理员：{$data['user']}的个人信息失败！");
                result(403, '修改失败！');
            }
        }
    }

    /**
     * 根据ID获取管理员数据
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        // 接收ID
        $id = Request::param('id');
        // 查询当前编辑的管理员
        $info = Db::view('admin', 'id,user,password,photo,name,card,sex,age,region,mobile,email,introduction,create_time,update_time,status,role_id')
            ->where('admin.id', $id)
            ->view('group', 'name as rolename', 'group.id=admin.id')
            ->find();
        result(200, "获取数据成功！", $info);
    }

    /**
     * 管理员删除
     * @throws \think\db\exception\DbException
     */
    public function delete()
    {
        if (request()->isDelete()) {
            //接收前台传过来的ID
            $id = Request::param('id');
            //转为数组
            $array = explode(',', $id);
            // 删除数组中空元素
            $array = array_filter($array);
            //判断是否存在超级管理员，是则不能删除
            if (!in_array(1, $array)) {
                //判断是否存在自己,是则不能删除
                if (!in_array(request()->uid, $array)) {
                    //进行删除操作
                    $res = Db::name('admin')->delete($array);
                    if ($res) {
                        $this->log("删除了管理员[ID:{$array}]");
                        result(200, "删除成功！");
                    } else {
                        $this->log("删除管理员[ID:{$array}]失败！");
                        result(403, "删除失败！");
                    }
                } else {
                    result(403, "自己不能删除！");
                }
            } else {
                result(403, "超级管理员不能可删除！");
            }
        }
    }

    /**
     * 修改管理员的状态
     * @throws \think\db\exception\DbException
     */
    public function statusEdit()
    {
        if (request()->isPut()) {
            // 接收管理员ID
            $data = Request::only(['id', 'status']);
            if ($data['id'] == 1 && $data['status'] == 0) {
                result(403, "超级管理员状态不能修改！");
            } elseif ($data['id'] == request()->uid && $data['status'] == 0) {
                result(403, "自己的状态不能修改！");
            } else {
                // 执行更新
                $admin = AdminModel::find($data['id']);
                $res = $admin->save($data);
                if ($res) {
                    $this->log("修改了管理员[id:{$data['id']}的状态！]");
                    result(200, "修改成功！");
                } else {
                    $this->log("修改管理员[id:{$data['id']}的状态失败！]");
                    result(403, "修改失败！");
                }
            }
        }
    }
}
