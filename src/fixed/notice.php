<?php
require_once __DIR__ . '/includes/db.php';

$sql = "SELECT * FROM notice_files";
$result = mysqli_query($conn, $sql);
$files = [];
while ($row = mysqli_fetch_assoc($result)) {
    $files[] = $row;
}

$file = isset($_GET['file']) ? $_GET['file'] : 'welcome.txt';
// 白名单校验:只允许访问这几个指定的公告文件
$allowed_files = ['welcome.txt', 'rules.txt', 'holiday.txt'];

if (!in_array($file, $allowed_files)) {
    die('不允许访问该文件');
}
$content = '';
$include_path = '';

/*
 * ============================================================
 * 漏洞点：文件包含漏洞(本地文件包含 LFI)
 * ------------------------------------------------------------
 * $file 直接拼接进include路径,没有做任何白名单校验或者路径过滤。
 *
 * 攻击示例:
 * notice.php?file=../includes/db.php
 * 可以包含并执行任意PHP文件的内容(如果目标文件是.php,
 * 里面的PHP代码会被当作代码执行,可能泄露数据库密码等敏感信息)
 *
 * notice.php?file=../../../../Windows/System32/drivers/etc/hosts
 * 尝试用目录穿越读取系统文件(如果文件不是.php后缀,
 * include还是会把内容当作HTML直接输出,同样能读到文件内容)
 *
 * 更严重的利用方式: 配合upload.php先上传一个图片马或者txt格式的
 * 恶意PHP代码文件,再用这里的文件包含漏洞把它include进来执行,
 * 这是文件上传+文件包含组合利用的经典攻击链
 * ============================================================
 */
$notice_dir = __DIR__ . '/templates/';
$target_file = $notice_dir . $file;

ob_start();
if (file_exists($target_file)) {
    include $target_file;
    $content = ob_get_clean();
} else {
    ob_end_clean();
    $content = '文件不存在: ' . htmlspecialchars($file);
}

$page_title = '公告查看';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h2>📋 校园公告</h2>
  <p style="font-size:13px;color:#999;">选择下方公告查看详细内容</p>
  <ul>
    <?php foreach ($files as $f): ?>
      <li><a href="notice.php?file=<?php echo htmlspecialchars($f['filename']); ?>"><?php echo htmlspecialchars($f['display_name']); ?></a></li>
    <?php endforeach; ?>
  </ul>
</div>

<div class="card">
  <h2>公告内容</h2>
  <div style="font-size:13px;color:#999;margin-bottom:10px;">当前文件参数: <code><?php echo htmlspecialchars($file); ?></code></div>
  <pre style="white-space:pre-wrap;line-height:1.8;"><?php echo $content; ?></pre>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
