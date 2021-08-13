<?php
/**
 * 提现控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;

class Withdraw extends Base
{
    /**
     * 提现列表
     * @throws \think\db\exception\DbException
     */
    public function list()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page', 'status']);
            if (empty($data['status'])) {
                //查询所有开发者
                $info = Db::name('developer_withdraw')
                    ->whereLike('developer_id', "%" . $data['keywords'] . "%")
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            } else {
                //查询所有开发者
                $info = Db::name('developer_withdraw')
                    ->whereLike('developer_id', "%" . $data['keywords'] . "%")
                    ->where('status', $data['status'])
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);
            }
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 提现订单审核通过操作
     * @throws \think\db\exception\DbException
     */
    public function pass()
    {
        if (Request::isPut()) {
            // 接收ID
            $id = Request::param('id');
            // 执行更新
            $res = Db::name('developer_withdraw')->where('id', $id)->update(['status' => '1']);
            if ($res) {
                $this->log("审核通过了提现订单[ID：{$id}]");
                result(200, "已通过！");
            } else {
                $this->log("审核通过提现订单[ID：{$id}]失败！");
                result(403, "操作失败！");
            }
        }
    }

    /**
     * 驳回提现订单操作
     * @throws \think\db\exception\DbException
     */
    public function reject()
    {
        if (request()->isPut()) {
            // 接收用户数据
            $data = Request::only(['id', 'cause']);
            // 执行更新
            $res = Db::name('developer_withdraw')->where('id', $data['id'])->update(['status' => '2', 'cause' => $data['cause']]);
            if ($res){
                $this->log("已驳回提现订单[ID：{$data['id']}]");
                result(200,"驳回成功！");
            }else{
                $this->log("驳回提现订单[ID：{$data['id']}]失败！");
                result(403,"驳回失败！");
            }
        }
    }
}
