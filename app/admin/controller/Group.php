<?php
/**
 * 权限组控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\model\Group as GroupModel;
use app\admin\validate\Group as GroupValidate;
class Group extends Base
{
    /**
     * 权限组列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //如果当前为超级管理员，则输出全部权限组信息
            if (request()->uid == 1) {
                //查询所有权限组信息
                $list = GroupModel::order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ])->each(function ($item, $key) {
                        // 查询当前所有管理员用该权限组的user
                        $admin = Db::name('admin')->where('role_id', $item['id'])->field('user')->select();
                        $arr = [];
                        foreach ($admin as $k => $v) {
                            $arr[$k] = $v['user'];
                        }
                        $item['user'] = $arr;
                    });
            } else {
                // 查询当前管理员正在使用的权限组
                $list = GroupModel::where('id', request()->uid)
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ])->each(function ($item, $key) {
                        // 查询当前所有管理员用该权限组的user
                        $admin = Db::name('admin')->where('role_id', $item['id'])->field('user')->select();
                        $arr = [];
                        foreach ($admin as $k => $v) {
                            $arr[$k] = $v['user'];
                        }
                        $item['user'] = $arr;
                    });
            }
            result(200, "获取数据成功！", $list->toArray());
        }
    }

    /**
     * 添加权限组
     * @throws \think\db\exception\DbException
     */
    public function add()
    {
        if (request()->isPost()){
            // 接收数据
            $data = Request::only(['name','instruction','status','rules']);
            // 验证数据
            $validate = new GroupValidate();
            if (!$validate->check($data)){
                result(403,$validate->getError());
            }
            // 执行添加
            $res = GroupModel::create($data);
            if ($res){
                // 记录日志
                $this->log("添加了权限组：{$data['name']}");
                result(200,"添加成功！");
            }else{
                // 记录日志
                $this->log("添加权限组：{$data['name']}失败！");
                result(403,"添加失败！");
            }
        }
    }

    /**
     * 编辑权限组
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        if (request()->isPut()){
            // 接收数据
            $data = Request::only(['id,name','instruction','rules']);
            // 验证数据
            $validate = new GroupValidate();
            if (!$validate->check($data)){
                result(403,$validate->getError());
            }
            // 执行更新
            $group = GroupModel::find($data['id']);
            $res = $group->save($data);
            if ($res){
                // 记录日志
                $this->log("修改了权限组[ID:{$data['id']}]的信息！");
                result(200,"修改成功！");
            }else{
                // 记录日志
                $this->log("修改权限组[ID:{$data['id']}]的信息失败！");
                result(403,"修改失败！");
            }
        }
    }

    /**
     * 删除权限组
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        if (request()->isDelete()){
            // 接收ID
            $id = Request::param('id');
            //转为数组
            $array = explode(',', $id);
            // 删除数组中空元素
            $array = array_filter($array);
            //判断是否存在超级权限的ID以及正在使用的ID，存在则不能删除
            if (!in_array(1,$array)){
                $info = Db::name('admin')->where('id',request()->uid)->field('role_id')->find();
                if (!in_array($info['role_id'],$array)){
                    //进行删除操作
                    $res = Db::name('group')->delete($array);
                    if ($res){
                        // 记录日志
                        $this->log("删除了权限组[ID：{$id}]");
                        result(200,"删除成功！");
                    }else{
                        // 记录日志
                        $this->log("删除权限组[ID：{$id}]失败！");
                        result(403,"删除失败！");
                    }
                }else{
                    result(403,"正在使用的权限组不能删除！");
                }
            }else{
                result(403,"超级权限组不能删除！");
            }
        }
    }

    /**
     * 根据ID查询权限组的数据
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        // 接收数据
        $id = Request::param('id');
        //查询数据
        $info = Db::name('group')->where('id',$id)->find();
        //切割字符串转为数组
        $info['rules'] = explode(',',$info['rules']);
        // 删除数组中空元素
        $info['rules'] = array_filter($info['rules']);
        //查询一级ID
        $one = Db::name('menu')->where('pid',0)->field(['id','name'])->select()->toArray();
        //循环一级数组
        foreach($one as $key => $val){
            //查询二级ID
            $two = Db::name('menu')->where('pid',$val['id'])->field(['id','name','pid'])->select()->toArray();
            $one[$key]['children'] = $two;
            //循环二级数组
            foreach ($one[$key]['children'] as $item => $value){
                $three = Db::name('menu')->where('pid',$value['id'])->field(['id','name,pid as ppid'])->select()->toArray();
                //循环将pid赋值给$three
                for ($i=0; $i<count($three); $i++){
                    $three[$i]['pid'] = $value['pid'];
                }
                $one[$key]['children'][$item]['children'] = $three;
            }
        }
        $info['children'] = $one;
        result(200,"获取数据成功！",$info);
    }

    /**
     * 权限组状态修改
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function statusEdit()
    {
        if (request()->isPut()){
            // 接收权限组ID
            $data = Request::only(['id', 'status']);
            $info = Db::name('admin')->where('id',request()->uid)->field('role_id')->find();
            if ($data['id'] == 1 && $data['status'] == 0) {
                result(403, "超级权限组的状态不能修改！");
            }elseif($data['id'] == $info['role_id'] && $data['status'] == 0){
                result(403,"正在使用的权限组不能修改状态！");
            }else{
                // 执行更新
                $group = GroupModel::find($data['id']);
                $res = $group->save($data);
                if ($res){
                    $this->log("修改权限组[ID:{$data['id']}]的状态成功！");
                    result(200,"修改成功！");
                }else{
                    $this->log("修改权限组[ID:{$data['id']}]的状态失败！");
                    result(403,"修改失败！");
                }
            }
        }
    }

}
