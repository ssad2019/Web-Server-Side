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
    global $userid;
    
    if (!isset($_POST['id']) || !isset($_POST['status'])) returnJson(400);
    if ($_POST['id'] == '' || $_POST['status'] == '') returnJson(400);
    $id = getOrderId($_POST['id']);
    if (!findOrder($userid, $id)) returnJson(400);

    editOrderStatus($id, $_POST['status']);

    returnJson(200);
}