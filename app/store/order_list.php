<?php
/**
 * 获取订单列表
 * 
 * 负责订单列表获取接口的实现
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
    
    if (!isset($_GET['count']) && !isset($_GET['offset'])) returnJson(400);
    if (isset($_GET['count']))
    {
        if($_GET['count'] == '') returnJson(400);
        $orderlist = getDESCList($userid, $_GET['count']);
    }
    else if(isset($_GET['offset']))
    {
        if($_GET['offset'] == '') returnJson(400);
        $offset = getOrderId($_GET['offset']);
        $orderlist = getOffList($userid, $offset);
    }

    if(empty($orderlist)) returnJson(200, null);

    returnJson(200, $orderlist);
}