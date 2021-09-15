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

    /**
     * 我的桌面
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
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
            // 顶部四大数据栏
            $moneyTotal = Db::name('user_buylog')->where('status', '1')->sum('money');
            $developer = Db::name('user_developer')->count();
            $user = Db::name('user')->count();
            $authorization = Db::name('authorization')->count();
            $data['head'] = [
                'money_total' => $moneyTotal,
                'developer' => $developer,
                'user' => $user,
                'authorization' => $authorization
            ];
            // 消费排行榜
            $buyMoney = Db::name('user')->field('id,user,expenditure')->order('expenditure', 'desc')->limit(5)->select();
            $data['buy_money'] = $buyMoney;
            // 应用排行榜
            $appDownload = Db::name('app')->field('id,name,download')->order('download', 'desc')->limit(5)->select();
            $data['app_download'] = $appDownload;
            // 新用户注册
            $newUser = Db::name('user')->field('id,user,create_time')->order('create_time', 'desc')->limit(5)->select();
            $data['new_user'] = $newUser;
            // 开发者排行榜
            $developerAll = Db::name('user_developer')->field('id,level')->select()->toArray();
            foreach ($developerAll as $key => $val) {
                // 过滤官方发布的应用
                if ($val['id'] !== 0) {
                    // 统计每个开发发布的应用数量
                    $developerAll[$key]['app_count'] = Db::name('app')->where('developer_id', $val['id'])->count();
                }
            }
            $developerAll = $this->developerSort($developerAll);
            if (count($developerAll) > 5) {
                for ($i = 0; $i < 5; $i++) {
                    $newDeveloper[] = $developerAll[$i];
                }
                $data['developer_data'] = $newDeveloper;
            } else {
                $data['developer_data'] = $developerAll;
            }
            // 每周报表
            $week = -6;
            while ($week <= 0) {
                // 每日收入统计
                $dayMoney = Db::name('user_buylog')->where('status', '1')->whereDay('create_time', date('Y-m-d', strtotime("{$week} day")))->sum('money');
                // 防止为NULL造成报错
                if (empty($dayMoney)) {
                    $dayMoney = 0;
                }
                $moneyWeekData[] = $dayMoney;
                // 每日用户注册统计
                $dayNewUser = Db::name('user')->whereDay('create_time', date('Y-m-d', strtotime("{$week} day")))->count();
                if (empty($dayNewUser)) {
                    $dayNewUser = 0;
                }
                $userWeekData[] = $dayNewUser;
                // 每日新增应用
                $dayApp = Db::name('app')->whereDay('create_time', date('Y-m-d', strtotime("{$week} day")))->count();
                if (empty($dayApp)) {
                    $dayApp = 0;
                }
                $appWeekData[] = $dayApp;
                // 每日授权数量
                $dayAuthorization = Db::name('authorization')->whereDay('create_time', date('Y-m-d', strtotime("{$week} day")))->count();
                if (empty($dayAuthorization)) {
                    $dayAuthorization = 0;
                }
                $authorizationWeekData[] = $dayAuthorization;
                // 每日时间记录
                $weekTime[] = date('Y-m-d', strtotime("{$week} day"));
                $week = $week + 1;
            }
            $data['option'] = [
                'legend' => [
                    'data' => ['收入金额', '用户注册', '新增应用', '授权数量']
                ],
                'xAxis' => [
                    ['data' => $weekTime]
                ],
                'series' => [
                    ['name' => '收入金额', 'type' => 'line', 'stack' => '总量', 'data' => $moneyWeekData],
                    ['name' => '用户注册', 'type' => 'line', 'stack' => '总量', 'data' => $userWeekData],
                    ['name' => '新增应用', 'type' => 'line', 'stack' => '总量', 'data' => $appWeekData],
                    ['name' => '授权数量', 'type' => 'line', 'stack' => '总量', 'data' => $authorizationWeekData]
                ]
            ];
            result(200, "获取数据成功！", $data);
        }
    }

    /**
     * 从大到小排序
     * @param array $array
     * @return array|mixed
     */
    public function developerSort($array = [])
    {
        // 统计当前数组的长度
        $length = count($array);
        for ($i = 1; $i < $length; $i++) {
            if ($array[$i - 1]['app_count'] < $array[$i]['app_count']) {
                for ($j = $i - 1; $j >= 0; $j--) {
                    $temp = $array[$j + 1]['app_count'];
                    if ($temp > $array[$j]['app_count']) {
                        $array[$j + 1]['app_count'] = $array[$j]['app_count'];
                        $array[$j]['app_count'] = $temp;
                    } else {
                        break;
                    }
                }
            }
        }
        return $array;
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
