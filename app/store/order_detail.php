<?php
/**
 * 查看订单详细信息
 * 
 * 负责订单详细信息查看接口的实现
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
        getdetail();
        break;
    default:
        returnJson(400);
}

function getdetail() {
    global $userid;
    
    if (!isset($_GET['id'])) returnJson(400);
    if (!findOrder($userid, $_GET['id'])) returnJson(400);

    $iteminfo = getListItem($userid, $_GET['id']);

    returnJson(200, $iteminfo);
}