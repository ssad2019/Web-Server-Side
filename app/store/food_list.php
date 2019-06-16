<?php
/**
 * 商品列表获取类
 * 
 * 负责商品列表信息的获取
 * 
 * @author  jiangxm9
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

    $foodList = getFoodList($userid);
    foreach ($foodList as $food) {
    	if($food['imgurl'] = '')
    		$food['imgurl'] = DEFAULT_AVATAR;
    }
    if(empty($foodList)){
        returnJson(200, null);
    }


    returnJson(200, $foodList);
}