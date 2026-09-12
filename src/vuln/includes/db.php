<?php
/**
 * 数据库连接配置
 * ------------------------------------------------------------
 * 本文件同时兼容两种部署方式:
 *
 * 【方式1: Windows本地部署(phpstudy/XAMPP)】
 * 不需要设置任何环境变量,会自动使用下面的默认值(localhost)。
 *
 * 【方式2: Docker部署】
 * docker-compose.yml 里会通过环境变量传入真实的数据库连接信息
 * (数据库地址会是MySQL容器的服务名,而不是localhost),
 * 代码会优先读取环境变量,读不到才使用下面的默认值。
 *
 * 两种部署方式使用的是同一份代码,不需要手动修改。
 */

// 关键: 显式声明HTTP响应头的字符集为UTF-8。
header('Content-Type: text/html; charset=utf-8');

// 关键: 显式禁用gzip压缩输出。
ini_set('zlib.output_compression', 'Off');
if (function_exists('apache_setenv')) {
    @apache_setenv('no-gzip', '1');
}

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'root';
$DB_NAME = getenv('DB_NAME') ?: 'campus_cms';
// 默认 3306（phpStudy/XAMPP 的 MySQL 标准端口）。
// 若你的 MySQL 使用其他端口（如 Docker 映射出的 3307），修改此处或设置环境变量 DB_PORT。
$DB_PORT = getenv('DB_PORT') ?: 3306;

$max_retry = 10;
$conn = false;
for ($i = 0; $i < $max_retry; $i++) {
    $conn = @mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    if ($conn) break;
    sleep(2);
}

if (!$conn) {
    die('数据库连接失败: ' . mysqli_connect_error());
}

// 显式设置连接字符集,并且检测这一步是否真的成功
$charset_result = mysqli_set_charset($conn, 'utf8mb4');

/*
 * ============================================================
 * 临时诊断代码: 访问 db.php?debug=1 可以直接看到字符集诊断信息
 * 排查完乱码问题后,建议删掉这一段,不要留在正式环境里
 * ============================================================
 */
if (isset($_GET['debug'])) {
    header('Content-Type: text/plain; charset=utf-8');

    echo "===== 字符集诊断信息 =====\n\n";

    echo "1. mysqli_set_charset() 是否成功: " . ($charset_result ? '是' : '否(失败!)') . "\n\n";

    echo "2. PHP mysqli连接实际使用的字符集: " . mysqli_character_set_name($conn) . "\n";
    echo "   (这里应该显示 utf8mb4,如果不是,说明连接字符集没设置成功)\n\n";

    echo "3. MySQL服务端各项字符集变量:\n";
    $result = mysqli_query($conn, "SHOW VARIABLES LIKE 'character_set%'");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "   {$row['Variable_name']} = {$row['Value']}\n";
    }
    echo "\n";

    echo "4. 直接查询一条新闻标题,原始字节(hex)对照:\n";
    $r = mysqli_query($conn, "SELECT title FROM news LIMIT 1");
    $row = mysqli_fetch_assoc($r);
    $title = $row['title'];
    echo "   标题内容: " . $title . "\n";
    echo "   字节长度: " . strlen($title) . "\n";
    echo "   前30字节hex: " . bin2hex(substr($title, 0, 30)) . "\n";
    echo "   (如果标题内容本身在这里显示就是乱码,说明PHP从数据库读出来\n";
    echo "   的那一刻就已经错了,问题出在mysqli连接字符集;\n";
    echo "   如果这里显示正常,但网页正文显示乱码,说明是HTTP传输/浏览器渲染环节的问题)\n\n";

    echo "5. PHP版本: " . phpversion() . "\n";
    echo "6. mysqli扩展版本: " . mysqli_get_client_info() . "\n";

    exit;
}

session_start();