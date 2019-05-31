<?php
/**
 * 杂项函数类
 * 
 * 此处放置一些无法明确分类的函数。
 * 
 * @author  MikuAlpha
 * @version 1.0
 */
include_once('../settings/settings.php');

//状态码列表
const STATUS_CODE = array(
    200 => 'OK',
    201 => 'Created',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not Found',
    429 => 'Too Many Requests',
    500 => 'Internal Server Error',
    502 => 'Bad Gateway',
    504 => 'Gateway Timeout',
    1001 => 'Username Length Too Short(<4)',
    1002 => 'Password Length Too Short(<8)',
    1003 => 'Username Length Too Long(>=20)',
    1004 => 'Password Length Too Long(>=20)',
    1005 => 'Invaild Username',
    1006 => 'Invaild Password',
    1010 => 'Invaild Token',
    1011 => 'Token Already Expired'
);

/**
 * 为JSON对象附加状态码，返回并中止此次处理
 * 
 * @param int $httpCode HTTP状态码
 * @param array $jsonObj 输入的JSON对象
 */
function returnJson($httpCode, $jsonObj = array())
{
    //如果状态码不存在于列表中，则返回500
    if (!array_key_exists($httpCode, STATUS_CODE)) {
        $httpCode = 500;
    }
    $outputs = array();
    $outputs['status'] = $httpCode;
    $outputs['msg'] = STATUS_CODE[$httpCode];
    if (count($jsonObj) > 0) $outputs['data'] = $jsonObj;
    die(json_encode($outputs));
}

/**
 * 验证当前协议是否为HTTPS
 * 
 * @return bool
 */
function isHttps()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
        return true;
    } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

/**
 * 生成图片链接
 * 
 * @param string $filename 存储的文件名
 * @return string 生成的链接
 */
function getImageLink($filename) {
    return (isHttps() ? 'https' : 'http') . '://' . HOST_NAME . '/pic.php?file=' . $filename;
}

/**
 * 获取图片类型
 * 
 * @param string $image 图片文件名
 * @return string 文件后缀
 */
function getImageType($image) {
    $type = strrchr($image, ".");
    $type = str_replace(".", "", $type);
    return $type;
}

/**
 * 获取图片并返回
 * 
 * @param string $filename 请求文件名
 * @return void
 */
function getImage($filename) {
    $url = "https://" . OSS_INTERNAL_DOMAIN  . '/' . $filename;

    switch (getImageType(($filename))) {
        case 'png':
            header('Content-Type:image/png');
            break;
        case 'jpg': case 'jpeg':
            header('Content-Type:image/jpeg');
            break;
        default:
            http_response_code(400);
            die();
    }
    die(file_get_contents($url));
}

