<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * curl post请求 只用于请求C++的接口
 * @param string $url  接口地址
 * @param array $data 参数
 * @return mixed
 * @author 000
 */
function post_curl_c($url, $api, $data)
{
    if(empty($url) || empty($api) || empty($data)){
        return false;
    }
    $data = json_encode($data);
    $ch   = curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: '.$api,
        'Content-Length: '.strlen($data)
    ));

    $result = curl_exec($ch);
    // 检查是否有错误发生
    if(curl_errno($ch)){
//         \Log::info('post_curl_c请求C++接口curl不通',['data'=>$data,'url'=>$url.'/'.$api,'curl_errno'=>curl_errno($ch)]);
        return false;
    }
    curl_close($ch);
    $status = json_decode($result);
    if ($status->result != 1) {
//         \Log::info('post_curl_c请求C++接口C++返回失败',['result'=>$status]);
    }

    return $result;
//     \Log::info('post_curl_c请求C++接口返回数据：',['result'=>$status]);
    return $status->result;
}

/**
 * 生成跟C++通讯的sign
 * @param $str
 * @return string
 */
function get_c_sign($str)
{
    return md5($str.'c12345678');
}