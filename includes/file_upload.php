<?php
/**
 * 文件上传类
 * 
 * 利用OSS进行文件上传并进行管理
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

require(__DIR__ . '/../vendor/autoload.php');

include(__DIR__ . '/../settings/settings.php');

use OSS\Core\OssException;
use OSS\OssClient;

/**
 * 上传图片
 * 
 * @param string $path 图片路径(可为空)
 * @param string $filename 图片文件名(可为空)
 * @return string 存储的文件名
 */
function uploadImage($path = '', $filename = '') {
    try {
        $ossClient = new OssClient(
            OSS_ACCESS_KEY_ID,
            OSS_ACCESS_KEY_SECRET,
            OSS_ENDPOINT,
            false
        );
    } catch (OssException $e) {
        printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
        printf($e->getMessage() . "\n");
        return null;
    }

    if ($path == '') $path = $_FILES['file']['tmp_name'];
    if ($filename == '') $filename = generateFileName();

    $ossClient->uploadFile(OSS_BUCKET_NAME, $filename, $path);
    return $filename;
}

/**
 * 生成存储文件名
 * 
 * @return 根据时间生成的文件名
 */
function generateFileName() {
    $ext = substr($_FILES['file']['name'], strrpos($_FILES['file']['name'], '.') + 1); //上传文件后缀
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    $dst = md5($msectime) . '.' . $ext; //上传文件名称
    return $dst;
}