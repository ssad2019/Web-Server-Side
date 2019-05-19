<?php
/**
 * 杂项函数类
 * 
 * 此处放置一些无法明确分类的函数。
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

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
