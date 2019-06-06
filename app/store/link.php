<?php
/**
 * 点餐链接生成接口
 * 
 * 生成对应商家的点餐链接
 * 
 * @author  MikuAlpha
 * @version 1.0
 */
include(__DIR__ . '/../../includes/functions.php');
include(__DIR__ . '/../../includes/database.php');
include(__DIR__ . '/../../includes/auth.php');
include(__DIR__ . '/../../settings/settings.php');

$userid = getUserId(verifyToken());
if (!$userid) returnJson(401);

$site = $_POST['site'];
if ($site == '') returnJson(400);

$url = (isHttps() ? 'https' : 'http') . '://' . HOST_NAME . '/order.php?s=' . $userid . '&n=' . $site;

returnJson(200, array('link' => $url));