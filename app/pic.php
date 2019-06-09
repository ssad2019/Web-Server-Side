<?php
/**
 * 图片资源访问类
 * 
 * 自内网中访问图片资源并返回
 * 
 * @author  MikuAlpha
 * @version 1.0
 */
include('../settings/settings.php');
include('../includes/functions.php');

if ($_GET['file'] == '') {
    http_response_code(404);
    die();
}

getImage($_GET['file']);