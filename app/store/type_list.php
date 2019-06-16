<?php
/**
 * 商品分类获取类
 * 
 * 负责商品分类获取接口的实现
 * 
 * @author  jjx
 * @version 1.0
 */
include(__DIR__ . '/../../includes/functions.php');
include(__DIR__ . '/../../includes/database.php');
include(__DIR__ . '/../../includes/auth.php');

$userid = getUserId(verifyToken());
if (!$userid) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getList();
        break;
    default:
        returnJson(400);
}

function getList() {
    global $userid;

    $typelist = getTypeList($userid);

    if(empty($typelist)){
    	$ii = 'null';
    	returnJson(200, $ii);
    }

    returnJson(200, $typelist);
}