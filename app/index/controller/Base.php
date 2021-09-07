<?php
/**
 * 模块公共控制器
 * by:小航 11467102@qq.com
 */
declare (strict_types=1);

namespace app\index\controller;

use app\BaseController;
use app\index\middleware\Auth;
use think\facade\Config;
use think\facade\Db;
use think\facade\Filesystem;

class Base extends BaseController
{
    /**
     * 检测登录中间件调用
     * @var string[]
     */
    protected $middleware = [Auth::class];

    /**
     * 上传文件
     * 支持文件name:image或者name:file
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload()
    {
        if (request()->isPost()) {
            // 获取表单上传文件
            $files = request()->file();
            // 查询文件存储类型
            $system = Db::name('system')->where('id', 1)->field('file_storage,images_storage')->find();
            // 上传到本地服务器
            try {
                // 验证文件后缀
                validate(['image|图片' => 'filesize:1567800|fileExt:jpg,jpeg,png,gif,ico,bmp|fileMime:image/png,image/jpeg,image/gif,image/bmp,image/x-ico', 'file|文件' => 'fileExt:zip,rar,7z|fileMime:application/octet-stream,application/zip,application/x-zip-compressed,application/octet-stream'])->check($files);
                if (!is_array($files)) {
                    // 判断上传的是图片还是文件
                    if (isset($files['file'])) {
                        $type = $system['file_storage'];
                    } else if (isset($files['image'])) {
                        $type = $system['images_storage'];
                    } else {
                        // 如果上传的键不符合规范则直接返回
                        result(403, "上传文件非法！");
                    }
                } else {
                    // 如果上传为多文件，那只能存储在本地
                    $type = "0";
                }
                switch ($type) {
                    case "0":
                        $disk = "public"; //存储在本地
                        $url = request()->domain() . "/storage";
                        break;
                    case "1":
                        $disk = "aliyun"; //存储在阿里云
                        $url = Config::get('filesystem.disks.aliyun.url');
                        break;
                    case "2":
                        $disk = "qcloud"; //存储在腾讯云
                        $url = Config::get('filesystem.disks.qcloud.cdn');
                        break;
                    case "3":
                        $disk = "qiniu"; //存储在七牛云
                        $url = Config::get('filesystem.disks.qiniu.url');
                        break;
                    default:
                        result(403, "请求错误！");
                }
                $saveName = [];
                foreach ($files as $file) {
                    if (is_array($file)) {
                        foreach ($file as $f) {
                            // 获取文件扩展名作存储路径
                            $fileType = $f->extension();
                            $saveName[] = $url . "/" . Filesystem::disk($disk)->putFile($fileType, $f);
                        }
                    } else {
                        // 获取文件扩展名作存储路径
                        $fileType = $file->extension();
                        $saveName[] = $url . "/" . Filesystem::disk($disk)->putFile($fileType, $file);
                    }
                }
                result(200, "上传成功！", $saveName);
            } catch (\think\exception\ValidateException $e) {
                result(500, $e->getMessage());
            }
        }
    }

    /**
     * 过滤XSS攻击
     * @param $val //如果是数组，可以遍历，递归遍历
     * @return string|string[]|null
     */
    public function removeXSS($val)
    {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $va[$k] = addslashes($v);//防止unicode跨站脚本攻击
            }
            $val = $va;
        } else {
            $val = addslashes($val);//防止unicode跨站脚本攻击
        }
        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';

        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

            // &#x0040 @ search for the hex values
            $val = preg_replace('/(&#[x|X]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
            // &# @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = array('alert', 'javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', 'confirm', 'eval', 'document');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                        $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                        $pattern .= ')?';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }
        return $val;
    }

}
