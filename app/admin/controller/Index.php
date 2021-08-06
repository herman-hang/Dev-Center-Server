<?php
/**
 * 后台首页控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use auth\Auth;
use think\facade\Db;
use think\facade\Request;

class Index extends Base
{
    /**
     * 后台首页
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        //菜单查询
        $menu = Db::name('menu')->where(['pid' => 0, 'status' => 1])->field('id,name,url')->order('sort', 'desc')->select()->toArray();
        $admin = Db::name('admin')->where('id',request()->uid)->field('rule_id')->find();
        $group = Db::name('group')->where('id', $admin['rule_id'])->field('rules')->find();
        //转数组
        $groupArray = explode(',',$group['rules']);
        if (request()->uid !== 1){
            foreach($menu as $key => $val){
                if (in_array($val['id'],$groupArray)){
                    $subMenu = Db::name('menu')->where(['pid'=>$val['id'],'status'=>1])->field('id,name,url')->order('sort','desc')->select();
                    foreach ($subMenu as $k => $va) {
                        if (in_array($va['id'],$groupArray)){
                            $menu[$key]['submenu'][$k] = $va;
                        }
                    }
                }else{
                    //遍历删除无权限的规则，即不渲染
                    unset($menu[$key]);
                }
            }
        }else{//超级管理员
            foreach($menu as $key => $val){
                $subMenu = Db::name('menu')->where(['pid'=>$val['id'],'status'=>1])->field('name,url')->order('sort','desc')->select();
                $menu[$key]['submenu'] = $subMenu;
            }
        }
        result(200,'获取菜单成功！',$menu);
    }

    public function welcome()
    {
        echo 'Welcome';
    }
}
