<?php
// 应用公共文件
use think\exception\HttpResponseException;
use think\Response;

/**
 * 返回封装后的API数据到客户端
 * @param mixed $data 要返回的数据
 * @param integer $code 返回的code
 * @param mixed $msg 提示信息
 * @param string $type 返回数据格式
 * @param array $header 发送的Header信息
 * @return Response
 */
function result(int $code = 0, $msg = '', $data = [], string $type = '', array $header = []): Response
{
    $result = [
        'code' => $code,
        'msg' => $msg,
        'time' => time(),
        'data' => $data,
    ];

    $type = $type ?: 'json';
    $response = Response::create($result, $type)->header($header);

    throw new HttpResponseException($response);
}