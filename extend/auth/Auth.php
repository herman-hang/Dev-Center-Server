<?php

namespace auth;

use think\facade\Db;
use think\facade\Config;
use think\facade\Session;
use think\facade\Request;

/**
 * 权限认证类
 * 功能特性：
 * 1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
 *      $auth=new Auth();  $auth->check('规则名称','用户id')
 * 2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
 *      $auth=new Auth();  $auth->check('规则1,规则2','用户id','and')
 *      第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，表示用户值需要具备其中一个条件即可。默认为or
 * 3，一个用户可以属于多个用户组(auth_group_access表 定义了用户所属用户组)。我们需要设置每个用户组拥有哪些规则(auth_group 定义了用户组权限)
 *
 * 4，支持规则表达式。
 *      在auth_rule 表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。
 */
class Auth
{
    /**
     * var object 对象实例
     */
    protected static $instance;
    //默认配置
    protected $config = [
        'auth_on' => 1, // 权限开关
        'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_group' => 'group', // 用户组数据表名
//        'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
        'auth_rule' => 'menu', // 权限规则表
        'auth_user' => 'admin', // 用户信息表
    ];

    /**
     * 类架构函数
     * Auth constructor.
     */
    public function __construct()
    {
        //可设置配置项 auth, 此配置项为数组。
        if ($auth = Config::get('auth')) {
            $this->config = array_merge($this->config, $auth);
        }
    }

    /**
     * 初始化
     * access public
     * @param array $options 参数
     * return \think\Request
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 检查权限
     * @param $name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param $uid  int           认证用户的id
     * @param int $type 认证类型
     * @param string $mode 执行check的模式
     * @param string $relation 如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * return bool               通过验证返回true;失败返回false
     */
    public function check($name, $uid, $type = 1, $mode = 'url', $relation = 'or')
    {
        if (!$this->config['auth_on']) {
            return true;
        }
        // 获取用户需要验证的所有有效规则列表
        $authList = $this->getAuthList($uid, $type);
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = [$name];
            }
        }
        $list = []; //保存验证通过的规则名
        if ('url' == $mode) {
            $REQUEST = unserialize(strtolower(serialize(Request::param())));
        }
        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ('url' == $mode && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {
                    //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } else {
                if (in_array($auth, $name)) {
                    $list[] = $auth;
                }
            }
        }
        if ('or' == $relation && !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ('and' == $relation && empty($diff)) {
            return true;
        }
        return false;
    }

    /**
     * 根据管理员id获取权限组,返回值为数组
     * @param  $uid int     管理员id
     * return array       管理员所属的用户组 array(
     *     array('uid'=>'用户id','role_id'=>'权限组id','name'=>'权限组名称','rules'=>'权限组拥有的规则id,多个,号隔开'),
     *     ...)
     */
    public function getGroups($uid)
    {
        static $groups = [];
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        // 转换表名
        $auth_user = $this->config['auth_user'];
        $auth_group = $this->config['auth_group'];
        // 执行查询
        $user_groups = Db::view($auth_user, 'role_id')
            ->view($auth_group, 'name,status,rules', "{$auth_user}.role_id={$auth_group}.id", 'LEFT')
            ->where("{$auth_user}.id='{$uid}' and {$auth_group}.status='1'")
            ->select();
        $groups[$uid] = $user_groups ?: [];
        return $groups[$uid];
    }

    /**
     * 获得权限列表
     * @param integer $uid 管理员id
     * @param integer $type
     * return array
     */
    protected function getAuthList($uid, $type)
    {
        static $_authList = []; //保存用户验证通过的权限列表
        $t = implode(',', (array)$type);
        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }
        if (2 == $this->config['auth_type'] && Session::has('_auth_list_' . $uid . $t)) {
            return Session::get('_auth_list_' . $uid . $t);
        }
        //读取用户所属用户组
        $groups = $this->getGroups($uid);
        $ids = []; //保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = [];
            return [];
        }
        $map = [
            ['type', '=', $type],
            ['id', 'in', $ids],
            ['status', '=', 1],
            ['level', 'in', '1,2']
        ];
        //读取用户组所有权限规则
        $rules = Db::name($this->config['auth_rule'])->where($map)->field('condition,url')->select();
        //循环规则，判断结果。
        $authList = []; //
        foreach ($rules as $rule) {
            if (!empty($rule['condition'])) {
                //根据condition进行验证
                $user = $this->getUserInfo($uid); //获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                //dump($command); //debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = strtolower($rule['url']);
                }
            } else {
                //只要存在就记录
                $authList[] = strtolower($rule['url']);
            }
        }
        $_authList[$uid . $t] = $authList;
        if (2 == $this->config['auth_type']) {
            //规则列表结果保存到session
            Session::set('_auth_list_' . $uid . $t, $authList);
        }
        return array_unique($authList);
    }

    /**
     * 获得用户资料,根据自己的情况读取数据库
     */
    function getUserInfo($uid)
    {
        static $userinfo = [];
        $user = Db::name($this->config['auth_user']);
        // 获取用户表主键
        $_pk = is_string($user->getPk()) ? $user->getPk() : 'uid';
        if (!isset($userinfo[$uid])) {
            $userinfo[$uid] = $user->where($_pk, $uid)->find();
        }
        return $userinfo[$uid];
    }

    //根据id获取权限组名称
    function getRole($uid)
    {
        try {
            $info = Db::view($this->config['auth_user'], 'role_id')
                ->view($this->config['auth_group'], 'name', "{$this->config['auth_user']}.role_id={$this->config['auth_group']}.id")
                ->where('id', $uid)
                ->find();
            return $info['name'];
        } catch (\Exception $e) {
            return '此管理员未授予权限组';
        }

    }

    /**
     * 授予用户权限
     */
    public function setRole($uid, $group_id)
    {
        $res = Db::name($this->config['auth_user'])
            ->where('id', $uid)
            ->update(['role_id' => $group_id]);
        return true;
    }
}
