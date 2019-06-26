<?php
/**
 * 商品管理类
 * 
 * 负责商品的添加、修改和删除
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
    case 'PUT':
        putFood();
        break;
    case 'POST':
        postFood();
        break;
    case 'DELETE':
    	removeFood();
        break;
    default:
        returnJson(400);
}

function putFood() {
	global $userid;
	parse_str(file_get_contents('php://input'), $data);
    if (!isset($data['typeid']) ||  !isset($data['foodname']) || !isset($data['price']) ||  !isset($data['description']) || !isset($data['imgurl'])) 
        returnJson(400);
    if(empty($data['typeid']) || empty($data['foodname']) || empty($data['price']))
        returnJson(400);
    if(!findType($userid, $data['typeid']))
        returnJson(400);
	$returnArray = addFood($userid, $data['typeid'], $data['foodname'], $data['price'], $data['description'], $data['imgurl']);
    if($returnArray['id'] == 0 || empty($returnArray))
        returnJson(400);
    returnJson(200, $returnArray);
}

function postFood() {
    global $userid;
    if (!isset($_POST['typeid']) ||  !isset($_POST['foodname']) || !isset($_POST['price']) ||  !isset($_POST['description']) || !isset($_POST['imgurl'])) 
        returnJson(400);
    if(empty($_POST['foodid']) || empty($_POST['typeid']) || empty($_POST['foodname']) || empty($_POST['price']))
        returnJson(400);
    if(!findFood($userid, $_POST['foodid']))
        returnJson(400);
    if(!findType($userid, $_POST['typeid']))
        returnJson(400);
	modifyFood($_POST['foodid'], $_POST['typeid'], $_POST['foodname'], $_POST['price'], $_POST['description'], $_POST['imgurl']);
	returnJson(200);
}

function removeFood() {
    global $userid;
	parse_str(file_get_contents('php://input'), $data);
    if (!isset($data['foodid']) || empty($data['foodid'])) 
        returnJson(400);
    if(!findFood($userid, $data['foodid']))
        returnJson(400);
	deleteFood($data['foodid']);
	returnJson(200);
}