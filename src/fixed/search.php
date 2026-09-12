<?php
require_once __DIR__ . '/includes/db.php';

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$results = [];

if ($keyword !== '') {
    /*
     * ============================================================
     * 漏洞点：SQL注入(字符串型注入,搜索框最常见的漏洞位置)
     * ------------------------------------------------------------
     * $keyword 直接拼接进LIKE查询,没有做任何过滤。
     *
     * 攻击示例:
     * 搜索框输入: ' union select 1,username,password,role,1,1,1 from users -- 
     * 会导致LIKE查询失效,变成union select联合查询,
     * 把users表的数据以"新闻标题"的形式显示出来
     *
     * 正常搜索输入: 运动会
     * 拼接后: SELECT * FROM news WHERE title LIKE '%运动会%'
     * ============================================================
     */
    // 修复: 用 mysqli_real_escape_string 转义关键词,
    // 单引号会被转成 \' 无法闭合LIKE查询,union注入随之失效
    $keyword_safe = mysqli_real_escape_string($conn, $keyword);
    $sql = "SELECT * FROM news WHERE title LIKE '%" . $keyword_safe . "%'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
    }
}

$page_title = '搜索新闻';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h2>🔍 搜索新闻</h2>
  <form method="get">
    <label>关键词</label>
    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="输入新闻标题关键词">
    <button class="btn" type="submit">搜索</button>
  </form>
  <?php if (!empty($sql)): ?>
    <div style="font-size:13px;color:#999;margin-top:8px;">调试信息 - 执行的SQL: <?php echo htmlspecialchars($sql); ?></div>
  <?php endif; ?>
</div>

<?php if ($keyword !== ''): ?>
<div class="card">
  <h2>搜索结果</h2>
  <?php if (empty($results)): ?>
    <p style="color:#999;">没有找到相关新闻</p>
  <?php else: ?>
    <?php foreach ($results as $news): ?>
    <div class="news-item">
      <a href="news.php?id=<?php echo (int)($news['id'] ?? 0); ?>"><?php echo htmlspecialchars($news['title'] ?? ''); ?></a>
      <div class="meta">作者: <?php echo htmlspecialchars($news['author'] ?? ''); ?></div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
