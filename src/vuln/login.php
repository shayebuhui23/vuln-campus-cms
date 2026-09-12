<?php
require_once __DIR__ . '/includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    /*
     * ============================================================
     * 漏洞点：SQL注入
     * ------------------------------------------------------------
     * 用户输入的 $username 和 $password 直接拼接进SQL语句，
     * 没有做任何过滤或者使用预处理语句。
     *
     * 攻击示例：
     * 用户名输入: admin' -- 
     * 密码随便填
     * 拼接后的SQL会变成:
     * SELECT * FROM users WHERE username='admin' -- ' AND password='xxx'
     * 后面的密码判断被注释掉了，直接以admin身份登录成功。
     *
     * 或者用户名输入: ' or '1'='1
     * 密码输入: ' or '1'='1
     * 拼接后WHERE条件恒成立，会查出第一条用户记录直接登录。
     * ============================================================
     */
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['uid'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = '用户名或密码错误';
        // 教学环境下把执行的SQL语句打印出来,方便学员直观看到拼接过程
        $error .= '<br><small style="color:#999;">调试信息 - 执行的SQL: ' . htmlspecialchars($sql) . '</small>';
    }
}

$page_title = '登录';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:420px;margin:40px auto;">
  <h2>用户登录</h2>
  <?php if ($error): ?><div class="flash error"><?php echo $error; ?></div><?php endif; ?>
  <form method="post">
    <label>用户名</label>
    <input type="text" name="username">
    <label>密码</label>
    <input type="password" name="password">
    <button class="btn" type="submit" style="width:100%;">登录</button>
  </form>
  <div style="margin-top:16px;font-size:13px;color:#999;">
    测试账号：zhangwei / zhangwei123
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
