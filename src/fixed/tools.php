<?php
require_once __DIR__ . '/includes/db.php';

$output = '';
$cmd = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip'])) {
    $ip = $_POST['ip'];
	// 用正则表达式校验,只允许合法的IP地址格式(数字和点号)
	if (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip)) {
		die('请输入合法的IP地址格式，例如 127.0.0.1');
	}

    /*
     * ============================================================
     * 漏洞点：命令执行漏洞(命令注入)
     * ------------------------------------------------------------
     * $ip 直接拼接进shell命令,没有做任何过滤或者校验格式,
     * 用户可以通过特殊符号(Windows下常用 & 或 |)拼接执行任意命令。
     *
     * 攻击示例(本靶场部署在Windows环境,用Windows的命令拼接符号):
     * 输入: 127.0.0.1 & whoami
     * 会在ping完127.0.0.1之后,额外执行whoami命令,
     * 把当前系统用户名也显示在结果里
     *
     * 输入: 127.0.0.1 & dir
     * 会额外列出当前目录下的文件,验证可以执行任意系统命令
     *
     * 输入: 127.0.0.1 & type C:\Windows\System32\drivers\etc\hosts
     * 可以读取系统任意文件内容
     * ============================================================
     */
    $cmd = "ping -n 2 " . $ip;
    $output = shell_exec($cmd . ' 2>&1');
}

$page_title = '系统工具';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h2>🔧 网络连通性测试(Ping)</h2>
  <p style="font-size:13px;color:#999;">输入IP地址或域名，测试服务器到目标地址的网络连通性</p>

  <form method="post">
    <label>目标地址</label>
    <input type="text" name="ip" value="<?php echo htmlspecialchars($_POST['ip'] ?? '127.0.0.1'); ?>" placeholder="例如 127.0.0.1">
    <button class="btn" type="submit">测试连通性</button>
  </form>

  <?php if ($cmd): ?>
    <div style="font-size:13px;color:#999;margin-top:10px;">调试信息 - 执行的命令: <?php echo htmlspecialchars($cmd); ?></div>
  <?php endif; ?>

  <?php if ($output): ?>
  <div style="margin-top:16px;">
    <h3 style="font-size:14px;">执行结果:</h3>
    <pre style="background:#1e1e1e;color:#0f0;padding:16px;border-radius:6px;overflow-x:auto;font-size:13px;"><?php echo htmlspecialchars($output); ?></pre>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
