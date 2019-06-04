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

    parse_str(file_get_contents('php://input'), $data);
    
    if (!isset($data['count']) && !isset($data['offset'])) returnJson(400);
    if (isset($data['count']))
    {
        $orderlist = getDESCList($userid, $data['count']);
    }
    else if(isset($data['offset']))
    {
        $orderlist = getOffList($userid, $data['offset']);
    }

    returnJson(200, $orderlist);
}