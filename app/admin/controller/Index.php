<?php
/**
 * 后台首页控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use auth\Auth;
use think\facade\Db;
use think\facade\Env;
use think\facade\Request;

class Index extends Base
{
    /**
     * 后台首页
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function home()
    {
        //菜单查询
        $menu = Db::name('menu')->where(['pid' => 0, 'status' => 1])->field('id,name,url')->order('sort', 'desc')->select()->toArray();
        $admin = Db::name('admin')->where('id', request()->uid)->field('role_id')->find();
        $group = Db::name('group')->where('id', $admin['role_id'])->field('rules')->find();
        //转数组
        $groupArray = explode(',', $group['rules']);
        if (request()->uid !== 1) {
            foreach ($menu as $key => $val) {
                if (in_array($val['id'], $groupArray)) {
                    $subMenu = Db::name('menu')->where(['pid' => $val['id'], 'status' => 1])->field('id,name,url')->order('sort', 'desc')->select();
                    foreach ($subMenu as $k => $va) {
                        if (in_array($va['id'], $groupArray)) {
                            $menu[$key]['children'][$k] = $va;
                        }
                    }
                } else {
                    //遍历删除无权限的规则，即不渲染
                    unset($menu[$key]);
                }
            }
        } else {//超级管理员
            foreach ($menu as $key => $val) {
                $subMenu = Db::name('menu')->where(['pid' => $val['id'], 'status' => 1])->field('name,url')->order('sort', 'desc')->select();
                $menu[$key]['children'] = $subMenu;
            }
        }
        result(200, '获取菜单成功！', $menu);
    }

    public function welcome()
    {
        if (request()->isGet()) {
            // 我的状态
            $admin = Db::name('admin')->where('id', request()->uid)->field('user,role_id,lastlog_time,lastlog_ip,login_sum')->find();
            $group = Db::name('group')->where('id', $admin['role_id'])->field('name')->find();
            $data['status'] = [
                ['key' => '当前登录者', 'value' => $admin['user']],
                ['key' => '所属权限组', 'value' => $group['name']],
                ['key' => '上次登录IP', 'value' => $admin['lastlog_ip']],
                ['key' => '上次登录时间', 'value' => $admin['lastlog_time']],
                ['key' => '登录总次数', 'value' => $admin['login_sum']]
            ];
            result(200, "获取数据成功！", $data);
        }
    }

    /**
     * 报表数据
     */
    public function echart()
    {
    }

    /**
     * 清除缓存
     */
    public function clear()
    {
        if (request()->isPost()) {
            // 删除运行目录
            if (delete_dir_file(root_path() . 'runtime')) {
                result(200, "清除成功！");
            } else {
                result(200, "清除成功！");
            }
        }
    }

    /**
     * 退出登录
     * @throws \think\db\exception\DbException
     */
    public function loginOut()
    {
        if (request()->isPost()) {
            // 获取当前客户端IP地址
            $ip = Request::ip();
            // 更新
            $res = Db::name('admin')->where('id', request()->uid)->update(['lastlog_time' => time(), 'lastlog_ip' => $ip]);
            if ($res) {
                $this->log("退出登录成功！", 1);
                result(200, "退出成功！");
            } else {
                $this->log("退出登录失败！", 1);
                result(403, "退出失败！");
            }
        }
    }
}
