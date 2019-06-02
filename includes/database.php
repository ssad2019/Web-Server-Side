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
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
        userid INTEGER NOT NULL,
        typename VARCHAR(32) NOT NULL,
        CONSTRAINT t_u_fk FOREIGN KEY (userid) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS menu (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
		userid INTEGER NOT NULL,
		typeid INTEGER NOT NULL,
        foodname VARCHAR(32) NOT NULL,
        price DECIMAL(8,2) NOT NULL,
        description VARCHAR(255) DEFAULT \'\',
        imgurl VARCHAR(255) DEFAULT \'\',
        CONSTRAINT m_u_fk1 FOREIGN KEY (userid) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT m_t_fk2 FOREIGN KEY (typeid) REFERENCES type (id) ON UPDATE CASCADE ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8');
    $mysql->query('CREATE TABLE IF NOT EXISTS list (
        id INTEGER AUTO_INCREMENT PRIMARY KEY,
		userid INTEGER NOT NULL,
		site INTEGER NOT NULL,
        ordertime Datetime NOT NULL,
        status Boolean NOT NULL,
        info Text NOT NULL,
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
    $stmt = $mysql->prepare("SELECT id FROM type WHERE userid = ? AND typename = ?");
    $stmt->bind_param("is", $userid, $typename);
    $stmt->execute();
    $stmt->store_result();
	
	if ($stmt->num_rows > 0) 
	{
		$stmt->bind_result($id);
    	$stmt->fetch();
		return array('typeid' => $id);
	}
	
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
 * 检查分类是否存在（安全起见，不应直接调用此函数）
 * 
 * @param integer $typeid 分类ID
 * @return bool 是否存在
 */
function findType($typeid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM type WHERE id = ?");
    $stmt->bind_param("i", $typeid);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
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
 * 获取商品分类列表
 * 
 * @param integer $userid 用户id
 * @return array 包含多个array，每个array记录分类的ID和名称
 */
function getTypeList($userid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id, typename FROM type WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();
    $data = array(); 
    $stmt->bind_result($id, $typename);
    while($stmt->fetch())
    {
    	$data[] = array('typeid' => $id, 'typename' => $typename);
    }

    $stmt->close();
    $mysql->close();

    return $data;
}

/** 
 * 检查菜品是否存在
 * 
 * @param int $foodid 菜品ID
 * @return bool 是否存在
 */
function findFood($foodid)
{
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT * FROM menu WHERE id = ?");
    $stmt->bind_param("i", $foodid);
    $stmt->execute();
    $stmt->store_result();

    $result = $stmt->num_rows;

    $stmt->close();
    $mysql->close();

    return ($result > 0);
}

/**
* 获取菜单列表
*
* @param int $userid 商品ID
* @return array 包含多个数组，每个数组包含$id，$typeid，名称($foodname)，价格($price)，描述($description)，商品图片($imgurl)的数组
*/
function getFoodList($userid) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("SELECT id, typeid, foodname, price, description, imgurl FROM menu WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows <= 0) return array();

    $stmt->bind_result($id, $typeid, $foodname, $price, $description, $imgurl);
    $foodList = array();
    while($stmt->fetch()) {
        $foodList[] = array('id' => $id, 'typeid' => $typeid, 'foodname' => $foodname, 'price' => $price, 'description' => $description, 'imgurl' => $imgurl);
    }

    $stmt->close();
    $mysql->close();

    return $foodList;
}

/**
* 添加菜品
*
* @param int $userid 商家id
* @param int $typeid 分类id
* @param string $foodname 菜品名称
* @param double $price 菜品价格
* @param string $description 菜品描述
* @param string $imgurl 菜品图片链接
* @return array 包含商品id($foodid)的数组
*/
function addFood($userid, $typeid, $foodname, $price, $description, $imgurl) {
    $mysql = initConnection();
    $stmt = $mysql->prepare("INSERT IGNORE INTO menu (userid, typeid, foodname, price, description, imgurl) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdss", $userid, $typeid, $foodname, $price, $description, $imgurl);
    $stmt->execute();
    $stmt->store_result();
    $foodid = $stmt->insert_id;

    $stmt->close();
    $mysql->close();

    return array('id' => $foodid);
}

/**
* 修改菜品
*
* @param int $foodid 菜品id
* @param int $typeid 分类id
* @param string $foodname 菜品名称
* @param double $price 菜品价格
* @param string $description 菜品描述
* @param string $imgurl 菜品图片链接
* @return void
*/
function modifyFood($foodid, $typeid, $foodname, $price, $description, $imgurl) {
    $mysql = initConnection();
    $stmt = $mysql->prepare( "UPDATE menu SET typeid = ?, foodname = ?, price = ?, description = ?, imgurl = ? WHERE id = ?");
    $stmt->bind_param("isdssi", $typeid, $foodname, $price, $description, $imgurl, $foodid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}

/**
* 删除菜品
*
* @param int $foodid 菜品id
* @return void
*/
function deleteFood($foodid) {
    $mysql = initConnection();
    $stmt = $mysql->prepare( "DELETE FROM menu WHERE id = ?");
    $stmt->bind_param("i", $foodid);
    $stmt->execute();
    $stmt->store_result();

    $stmt->close();
    $mysql->close();
}
