<?php
/**
 * 开发者控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\Request;
use app\index\validate\Developer as DeveloperValidate;

class Developer extends Base
{
    /**
     * 获取开发者申请条件
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function developerConfig()
    {
        if (request()->isGet()) {
            // 查询申请开发者条件
            $developer = Db::name('developer_config')->where('id', 1)->field('condition')->find();
            $user = Db::name('user')->where('id', 1)->field('is_developer,cause')->find();
            // 申请条件
            $data['condition'] = $developer['condition'];
            // 查询申请进度
            $data['is_developer'] = $user['is_developer'];
            // 如果被驳回，则有驳回原因
            $data['cause'] = $user['cause'];
            result(200, "获取数据成功！", $data);
        }
    }

    /**
     * 提交申请成为开发者
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function becomeDeveloper()
    {
        if (request()->isPost()) {
            // 接收数据
            $data = Request::param();
            // 验证数据
            $validate = new DeveloperValidate();
            if (!$validate->sceneBecomeDeveloper()->check($data)) {
                result(403, $validate->getError());
            }
            // 查询是否已经是开发者
            $user = Db::name('user')->where('id', request()->uid)->field('mobile,email,is_developer')->find();
            if (empty($user['mobile']) || empty($user['email'])) {
                result(403, "请到个人资料绑定手机号码和邮箱！");
            }
            if ($user['is_developer'] == '2') {
                result(403, "您已经是开发者，请勿重复申请！");
            }
            // xss过滤
            $data = $this->removeXSS($data);
            // 查询当前的开发者所有等级的服务费率
            $level = Db::name('developer_config')->where('id', 1)->find();
            // 用户ID
            $data['user_id'] = request()->uid;
            // 服务费率
            $data['brokerage'] = $level['copper'];
            // 服务等级
            $data['level'] = '0';
            // 查询开发者数据表中是否已经存在数据，存在则直接更新
            $developer = Db::name('user_developer')->where('id', request()->uid)->find();
            if ($developer) {
                Db::name('user')->where('id', request()->uid)->update(['is_developer' => '1']);
                Db::name('user_developer')->where('id', request()->uid)->update($data);
                result(201, "提交成功！");
            } else {
                // 执行添加
                $res = Db::name('user_developer')->insert($data);
                if ($res) {
                    // 将用户是否为开发者变更为审核中
                    Db::name('user')->where('id', request()->uid)->update(['is_developer' => '1']);
                    result(201, "提交成功！");
                } else {
                    result(403, "提交失败！");
                }
            }
        }
    }
}
