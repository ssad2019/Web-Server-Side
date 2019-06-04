<?php
/**
 * 点餐接口
 * 
 * 负责安卓端获取商家和菜品信息以及上传订单
 * 
 * @author  jiangxm9
 * @version 1.0
 */
include(__DIR__ . '/../includes/functions.php');
include(__DIR__ . '/../includes/database.php');
include(__DIR__ . '/../includes/auth.php');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getInfo();
        break;
    case 'POST':
        postOrder();
        break;
    default:
        returnJson(400);
}

function getInfo() {
	parse_str(file_get_contents('php://input'), $data);
	if (!isset($data['s']) || !isset($data['secret'])) 
		returnJson(400);
	if($data['secret'] != 123456)
		returnJson(400);
	$userInfo = getUserInfo($data['s']);
	$foodInfo = getFoodInfo($data['s']);

	//若头像为空，则设置为默认头像
    if ($userInfo['icon'] == '') 
    	$userInfo['icon'] = DEFAULT_AVATAR;

	$info = array('name' => $userInfo['name'], 'description' => $userInfo['description'], 'icon' => $userInfo['icon'], 'goods' => $foodInfo);
	returnJson(200, $info);
}

function postOrder() {
	parse_str(file_get_contents('php://input'), $data);
	if (!isset($data['s']) || !isset($data['n']) || !isset($data['order'])) 
		returnJson(400);
	$returnArray = addOrder($data);
	returnJson(200, $returnArray);
}