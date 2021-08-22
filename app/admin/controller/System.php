<?php
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\validate\System as SystemValidate;
use app\admin\validate\Admin as AdminValidate;

class System extends Base
{
    /**
     * 系统设置
     *
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function system()
    {
        //当前系统信息
        $system = Db::name('system')->where('id', 1)->find();
        result(200, "获取系统设置信息成功！", $system);
    }

    /**
     * 系统设置信息修改
     * @throws \think\db\exception\DbException
     */
    public function systemEdit()
    {
        //接收所有提交数值
        $data = Request::except(['file_storage', 'max_logerror', 'ip']);
        //实例化
        $validate = new SystemValidate;
        //验证数据
        if (!$validate->check($data)) {
            result(403, $validate->getError());
        }
        //执行更新操作
        $res = Db::name('system')->where('id', 1)->update($data);
        if ($res) {
            //记录日志
            $this->log("修改了系统信息！");
            result(200, "修改成功！");
        } else {
            $this->log("修改系统信息失败！");
            result(403, "修改失败！");
        }
    }

    /**
     * 安全配置
     */
    public function security()
    {
        //当前的信息
        $info = Db::name('system')->where('id', 1)->field('file_storage,max_logerror,ip,sms_type')->find();
        result(200, "获取安全配置信息成功！", $info);
    }

    /**
     * 安全配置编辑
     * @throws \think\db\exception\DbException
     */
    public function securityEdit()
    {
        //接收数值
        $data = Request::only(['file_storage', 'max_logerror', 'ip', 'sms_type']);
        // 判断该参数是否为数字或者是数字字符串
        if (!is_numeric($data['max_logerror'])) {
            result(403, "允许登录错误次数只能是数字！");
        }
        // 执行更新
        $res = Db::name('system')->where('id', 1)->update($data);
        if ($res) {
            $this->log("修改了安全配置信息！");
            result(200, "修改成功！");
        } else {
            $this->log("修改安全配置信息失败！");
            result(403, "修改失败！");
        }
    }

    /**
     * 开关管理
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function switch()
    {
        //查询所有开关信息
        $info = Db::name('switch')->where('id', 1)->find();
        result(200, "获取开关信息成功！", $info);
    }

    /**
     * 开关管理编辑
     * @throws \think\db\exception\DbException
     */
    public function switchEdit()
    {
        //接收前台传过来的数值
        $data = Request::param();
        $res = Db::name('switch')->where('id', 1)->update($data);
        if ($res) {
            $this->log("修改了开关管理信息！");
            result(200, "修改成功！");
        } else {
            $this->log("修改开关管理失败！");
            result(403, "修改失败！");
        }
    }

    /**
     * 修改密码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pass()
    {
        //查询当前管理员用户名
        $info = Db::name('admin')->where('id', request()->uid)->field('id,user')->find();
        result(200, "获取用户信息成功！", $info);
    }

    /**
     * 修改密码编辑
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function passEdit()
    {
        //接收数据
        $data = Request::param();
        //查询当前管理员密码
        $info = Db::name('admin')->where('id', request()->uid)->field('password')->find();
        //对数据进行验证
        $validate = new AdminValidate();
        if (!$validate->scenepassEdit()->check($data)) {
            result(403, $validate->getError());
        }
        //判断原始密码是否正确
        if (password_verify($data['mpassword'], $info['password'])) {
            //对密码进行加密
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            // 执行更新
            $res = Db::name('admin')->where('id', request()->uid)->update(['password' => $data['password']]);
            if ($res) {
                $this->log("修改了密码！");
                result(200, '修改成功！');
            } else {
                $this->log("修改了密码失败！");
                result(403, '修改失败！');
            }
        } else {
            result(403, '原始密码错误！');
        }
    }
}
