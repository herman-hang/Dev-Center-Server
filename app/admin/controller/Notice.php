<?php
/**
 * 通知公告控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use app\admin\validate\Notice as NoticeValidate;
use app\admin\model\Notice as NoticeModel;
class Notice extends Base
{
    /**
     * 公告列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()){
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有用户
            $info = Db::name('notice')
                ->whereLike('title|content', "%" . $data['keywords'] . "%")
                ->withoutField(['wx_openid','qq_openid','weibo_openid'])
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
     * 发布公告
     * @throws \think\db\exception\DbException
     */
    public function add()
    {
        if (request()->isPost()){
            // 接收数据
            $data = Request::except(['create_time', 'update_time']);
            // 验证数据
            $validate = new NoticeValidate();
            if (!$validate->sceneAdd()->check($data)){
                result(403,$validate->getError());
            }
            // 执行添加
            $res = NoticeModel::create($data);
            if ($res){
                $this->log("发布了公告：{$data['title']}");
                result(201,"添加成功！");
            }else{
                $this->log("发布公告：{$data['title']}失败！");
                result(403,"添加失败！");
            }
        }
    }

    /**
     * 编辑公告
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        if (request()->isPut()){
            // 接收数据
            $data = Request::except(['status','create_time', 'update_time']);
            // 验证数据
            $validate = new NoticeValidate();
            if (!$validate->sceneEdit()->check($data)){
                result(403,$validate->getError());
            }
            // 执行更新
            $notice = NoticeModel::find($data['id']);
            $res = $notice->save($data);
            if ($res){
                $this->log("修改了公告《{$data['title']}》！");
                result(200,"修改成功！");
            }else{
                $this->log("修改公告《{$data['title']}》失败！");
                result(403,"修改失败！");
            }
        }
    }

    /**
     * 删除公告
     * @throws \think\db\exception\DbException
     */
    public function delete()
    {
        if (request()->isDelete()){
            $id = Request::param('id');
            //转为数组
            $array = explode(',', $id);
            // 删除数组中空元素
            $array = array_filter($array);
            // 删除操作
            $res = Db::name('notice')->delete($array);
            // 转为字符串
            $array = implode(',',$array);
            if ($res) {
                $this->log("删除了公告[ID：{$array}]");
                result(200, "删除成功！");
            } else {
                $this->log("删除公告[ID：{$array}]失败！");
                result(200, "删除失败！");
            }
        }
    }

    /**
     * 根据ID查询公告信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function query()
    {
        // 接收ID
        $id = Request::param('id');
        // 查询公告信息
        $info = Db::name('notice')->where('id', $id)->find();
        result(200, "获取数据成功！",$info);
    }

    /**
     * 编辑公告状态
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
            $user = NoticeModel::find($data['id']);
            $res = $user->save($data);
            if ($res) {
                $this->log("修改了公告[id:{$data['id']}]的状态！");
                result(200, "修改成功！");
            } else {
                $this->log("修改公告[id:{$data['id']}]的状态失败！");
                result(403, "修改失败！");
            }
        }
    }
}
