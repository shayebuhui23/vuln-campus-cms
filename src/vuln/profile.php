<?php
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['uid'])) {
    header('Location: login.php');
    exit;
}

/*
 * ============================================================
 * 漏洞点：水平越权(顺带复习之前越权课程的知识点)
 * ------------------------------------------------------------
 * 直接用URL传入的id去查询用户资料,没有校验这个id
 * 是否是当前登录用户自己。
 *
 * 攻击示例:
 * profile.php?id=1
 * 可以看到id=1(管理员)的邮箱、手机号等信息,
 * 不需要任何密码,只要是登录状态就能看别人的资料。
 *
 * 这一页的id同时也存在SQL注入风险(未做整型强制转换),
 * 可以和news.php的漏洞点做对比讲解。
 * ============================================================
 */
$id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['uid'];

$sql = "SELECT id, username, role, email, phone, real_name FROM users WHERE id=$id";
$result = mysqli_query($conn, $sql);

$page_title = '个人资料';
require_once __DIR__ . '/includes/header.php';

if (!$result) {
    echo '<div class="card"><div class="flash error">查询出错: ' . htmlspecialchars(mysqli_error($conn)) . '</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo '<div class="card"><div class="flash error">未找到该用户</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>
<div class="card">
  <h2>👤 个人资料</h2>
  <table>
    <tr><th style="width:120px;">用户ID</th><td><?php echo htmlspecialchars($user['id']); ?></td></tr>
    <tr><th>用户名</th><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
    <tr><th>角色</th><td><?php echo htmlspecialchars($user['role']); ?></td></tr>
    <tr><th>真实姓名</th><td><?php echo htmlspecialchars($user['real_name']); ?></td></tr>
    <tr><th>邮箱</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
    <tr><th>手机号</th><td><?php echo htmlspecialchars($user['phone']); ?></td></tr>
  </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
