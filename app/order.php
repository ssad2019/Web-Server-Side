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
	if (!isset($_GET['s'])) returnJson(400);
	if(!findUserID($_GET['s']))
        returnJson(400);
	if (!isset($_GET['secret'])) die(file_get_contents('./404.html'));

	$userInfo = getUserInfo($_GET['s']);
	$foodInfo = getFoodInfo($_GET['s']);

	//若头像为空，则设置为默认头像
    if ($userInfo['icon'] == '') 
    	$userInfo['icon'] = DEFAULT_AVATAR;

	$info = array('name' => $userInfo['name'], 'description' => $userInfo['description'], 'icon' => $userInfo['icon'], 'goods' => $foodInfo);
	returnJson(200, $info);
}

function postOrder() {
	if (!isset($_POST['s']) || !isset($_POST['n']) || !isset($_POST['order'])) 
		returnJson(400);
	if(!findUserID($_POST['s']))
        returnJson(400);
    $data = json_decode($_POST['order'], true);
    if ($data && (is_object($data)) || (is_array($data) && !empty($data))) {
        $returnArray = addOrder($_POST);
		returnJson(200, $returnArray);
    }
    else{
    	returnJson(400);
    }
	
}