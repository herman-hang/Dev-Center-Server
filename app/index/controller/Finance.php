<?php
/**
 * 财务管理控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\Request;

class Finance extends Base
{
    /**
     * 消费明细
     * @throws \think\db\exception\DbException
     */
    public function buyLog()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            //查询所有用户
            $info = Db::name('user_buylog')
                ->whereLike('indent|uid|product_id', "%" . $data['keywords'] . "%")
                ->where('uid', request()->uid)
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
     * 提现记录
     * @throws \think\db\exception\DbException
     */
    public function withdrawList()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            $info = Db::name('developer_withdraw')
                ->whereLike('developer_id|indent', "%" . $data['keywords'] . "%")
                ->where('user_id', request()->uid)
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

    /**
     * 我的收入
     * @throws \think\db\exception\DbException
     */
    public function myIncome()
    {
        if (request()->isGet()) {
            // 接收数据
            $data = Request::only(['keywords', 'per_page', 'current_page']);
            $app = Db::name('app')->where('user_id', request()->uid)->column('id');
            if (!empty($app)){
                $info = Db::name('user_buylog')
                    ->whereLike('indent|uid|product_id', "%" . $data['keywords'] . "%")
                    ->where('product_id', 'between', $app)
                    ->where('status', '1')
                    ->order('create_time', 'desc')
                    ->paginate([
                        'list_rows' => $data['per_page'],
                        'query' => request()->param(),
                        'var_page' => 'page',
                        'page' => $data['current_page']
                    ]);

            }else{
                $info = [];
            }
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 查询总消费和余额
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function myWallet()
    {
        if (request()->isGet()) {
            // 查询总消费以及余额
            $info = Db::name('user')->where('id', request()->uid)->field('money,expenditure')->find();
            result(200, "获取数据成功！", $info);
        }
    }

    /**
     * 申请提现
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function applyWithdraw()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::only(['apply_money', 'account']);
            // 查询当前用户的余额有多少
            $user = Db::name('user')->where('id', request()->uid)->field('money')->find();
            $surplus = $user['money'] - $data['apply_money'];
            if ($surplus < 0) {
                result(403, "余额不足！");
            }
            // 执行更新
            $res = Db::name('user')->where('id', request()->uid)->update(['money' => $surplus]);
            if ($res) {
                // 查询开发者ID
                $developer = Db::name('user_developer')->where('user_id', request()->uid)->field('id')->find();
                // 查询申请提现订单
                Db::name('developer_withdraw')->insert(['developer_id' => $developer['id'], 'money' => $data['apply_money'], 'create_time' => time(), 'indent' => trade_no(), 'withdraw_account' => $data['account'], 'user_id' => request()->uid]);
                result(200, "申请成功，请等待申请！");
            } else {
                result(403, "申请失败！");
            }
        }
    }
}
