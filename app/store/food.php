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
	$returnArray = addFood($userid, $data['typeid'], $data['foodname'], $data['price'], $data['description'], $data['imgurl']);
    returnJson(200, $returnArray);
}

function postFood() {
	if (!isset($_POST['foodid']) || !isset($_POST['typeid']) || !isset($_POST['foodname']) || !isset($_POST['price']) || !isset($_POST['description']) || !isset($_POST['imgurl'])) 
		returnJson(400);
	modifyFood($_POST['id'], $_POST['typeid'], $_POST['foodname'], $_POST['price'], $_POST['description'], $_POST['imgurl']);
	returnJson(200);
}

function removeFood() {
	parse_str(file_get_contents('php://input'), $data);
	deleteFood($data['foodid']);
	returnJson(200)
}