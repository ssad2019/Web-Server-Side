<?php
/**
 * 商家信息修改类
 * 
 * 负责商家信息修改接口的实现
 * 
 * @author  MikuAlpha
 * @version 1.0
 */
include('../../includes/functions.php');
include('../../includes/database.php');
include('../../includes/auth.php');

$userid = getUserId(verifyToken());
if (!$userid) returnJson(401);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getInfo();
        break;
    case 'POST':
        postInfo();
        break;
    default:
        returnJson(400);
}

function getInfo() {
    global $userid;

    $userInfo = getUserInfo($userid);

    //若头像为空，则设置为默认头像
    if ($userInfo['icon'] == '') $userInfo['icon'] = DEFAULT_AVATAR;

    returnJson(200, $userInfo);
}

function postInfo() {
    global $userid;
    if (!isset($_POST['name']) || !isset($_POST['description'])) returnJson(400);
    editUserInfo($userid, $_POST['name'], $_POST[ 'description']);
    returnJson(200);
}