<?php
/**
 * 商品分类修改类
 * 
 * 负责商品分类修改接口的实现
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
    case 'PUT':
        add();
        break;
    case 'DELETE':
        delete();
        break;
    default:
        returnJson(400);
}

function add() {
    global $userid;
    
    parse_str(file_get_contents('php://input'), $data);

    if (!isset($data['typename'])) returnJson(400);
    $type = addType($userid, $data['typename']);

    returnJson(200, $type);
}

function delete() {
    parse_str(file_get_contents('php://input'), $data);
    
    if (!isset($data['typeid'])) returnJson(400);
    if (!findType($data['typeid'])) returnJson(400);
    deleteType($data['typeid']);

    returnJson(200);
}