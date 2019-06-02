<?php
/**
 * 数据库管理类
 * 
 * 负责构建SQL语句，与数据库进行交互，并生成接口提供给上一层。
 * 
 * @author  MikuAlpha
 * @version 1.0
 */

include_once(__DIR__ . '/../settings/settings.php');

createTables();

/**
 * 初始化MySQL连接
 * 
 * @return mysqli MySQL连接对象
 */
function initConnection()
{
    $mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysql->connect_error) die($mysql->connect_error);
    return $mysql;
}

/**
 * 当数据库表不存在时，创建表
 * 
 * @return void
 */
function createTables()
{
    $mysql = initConnection();
    $mysql->query('CREATE TABLE IF NOT EXISTS user (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(32) UNIQUE NOT NULL,
        passwd VARCHAR(255) NOT NULL,
        nickname VARCHAR(255) DEFAULT \'\',
        description VARCHAR(255) DEFAULT \'\',
        avatar VARCHAR(255) DEFAULT \'\'
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS type (
        id INTEGER AUTO_INCREMENT,
        userid INTEGER NOT NULL,
        typename VARCHAR(32) NOT NULL,
        PRIMARY KEY (userid, typename),
        CONSTRAINT t_u_fk FOREIGN KEY (userid) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS menu (
        id INTEGER AUTO_INCREMENT,
		userid INTEGER NOT NULL,
		typeid INTEGER NOT NULL,
        foodname VARCHAR(32) NOT NULL,
        price DECIMAL(8,2) NOT NULL,
        description VARCHAR(255) DEFAULT \'\',
        imgurl VARCHAR(255) DEFAULT \'\',
        PRIMARY KEY (userid, typeid, foodname),
        CONSTRAINT m_u_fk1 FOREIGN KEY (userid) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT m_t_fk2 FOREIGN KEY (typeid) REFERENCES type (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS order (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
		userid INTEGER NOT NULL,
		site INTEGER NOT NULL,
        ordertime Datetime NOT NULL,
        status Boolean NOT NULL,
        info Text DEFAULT \'\',
        imgurl VARCHAR(255) DEFAULT \'\',
        CONSTRAINT o_u_fk FOREIGN KEY (userid) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8');

    //ENGINE = InnoDB 
    if ($mysql->error) die($mysql->error);
    $mysql->close();
}

/**
 * 获取对应用户存储在数据库的哈希值，便于进行验证
 * 
 * @param string $user
 * @return string|bool 成功时返回Hash值，失败时返回false
 */
function getUserPasswdHash($user)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT passwd FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return false;

    $stmt->bind_result($passwd_hash);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return $passwd_hash;
}

/** 
 * 检查用户是否存在（安全起见，不应直接调用此函数）
 * 
 * @param string $user 用户名
 * @return bool 是否存在
 */
function findUser($user)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/** 
 * 添加用户
 * 
 * @param string $username 用户名
 * @param string $passwd 密码Hash值
 * @return void
 */
function addUser($username, $passwd)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO user (username, passwd) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $passwd);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 更改商家头像
 * 
 * @param int $userid 用户ID
 * @param string $image 头像链接
 * @return void
 */
function editUserAvatarLink($userid, $image)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("UPDATE user SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $image, $userid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
 * 获取商家信息
 * 
 * @param int $userid 用户ID
 * @return array 包含昵称($nickname)、描述($description)、图标($icon)的数组
 */
function getUserInfo($userid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT nickname, description, avatar FROM user WHERE id = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($nickname, $description, $avatar);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return array('name' => $nickname, 'description' => $description, 'icon' => $avatar);
}

/**
 * 修改用户信息
 * 
 * @param int $userId 用户ID
 * @param string $nickname 用户昵称
 * @param string $description 用户描述
 * @return void
 */
function editUserInfo($userId, $nickname, $description = "")
{
    $mysql = initConnection();
    $stmt = $mysql->prepare( "UPDATE user SET nickname = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nickname, $description, $userId);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/** 
 * 添加商品分类
 * 
 * @param integer $userid 用户id
 * @param string $typename 分类名称
 * @return array 包含分类ID($typeid)
 */
function addType($userid, $typename)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO type (userid, typename) VALUES (?, ?)");
    $stmt->bind_param("is", $userid, $typename);
    $stmt->execute();
    $stmt->store_result();

    $typeid = $stmt->insert_id;

    $stmt->close();
    $mysql->close();
    return array('typeid' => $typeid);
}

/** 
 * 删除商品分类
 * 
 * @param integer $typeid 分类id
 * @return void
 */
function deleteType($typeid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("DELETE FROM type where id = ?");
    $stmt->bind_param("i", $typeid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/** 
 * 获取商品分类
 * 
 * @param integer $userid 用户id
 * @return void
 */
function getType($userid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id, typename FROM type WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($id, $typename);
    $stmt->fetch();

    $stmt->close();
    $mysql->close();

    return array('name' => $nickname, 'description' => $description, 'icon' => $avatar);
}