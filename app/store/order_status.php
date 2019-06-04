<?php
/**
 * 修改订单状态
 * 
 * 负责订单状态修改接口的实现
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
    case 'POST':
        modify();
        break;
    default:
        returnJson(400);
}

function modify() {
    parse_str(file_get_contents('php://input'), $data);
    
    if (!isset($data['id']) || !isset($data['status'])) returnJson(400);
    if (!findOrder($data['id'])) returnJson(400);

    editOrderStatus($data['id'], $data['status']);

    returnJson(200);
}