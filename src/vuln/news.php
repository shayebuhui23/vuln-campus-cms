<?php
require_once __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? $_GET['id'] : 1;

/*
 * ============================================================
 * 漏洞点：SQL注入(GET参数,数字型注入)
 * ------------------------------------------------------------
 * $id 直接从URL参数拿过来拼接进SQL,没有强制转换成int,
 * 也没有做任何过滤。
 *
 * 攻击示例:
 * news.php?id=1 and 1=2 union select 1,username,password,role,1,1,1 from users
 * 可以把users表的账号密码回显在新闻标题/内容的位置上
 *
 * news.php?id=1'
 * 会直接导致SQL语法错误,报错信息里可能暴露数据库结构
 * ============================================================
 */
$sql = "SELECT * FROM news WHERE id=$id";
$result = mysqli_query($conn, $sql);

$page_title = '新闻详情';
require_once __DIR__ . '/includes/header.php';

if (!$result) {
    echo '<div class="card"><div class="flash error">SQL执行出错: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    echo '<div style="font-size:13px;color:#999;">调试信息 - 执行的SQL: ' . htmlspecialchars($sql) . '</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$news = mysqli_fetch_assoc($result);

if (!$news) {
    echo '<div class="card"><div class="flash error">未找到该新闻</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>
<div class="card">
  <h2><?php echo htmlspecialchars($news['title']); ?></h2>
  <div style="color:#999;font-size:13px;margin-bottom:16px;">
    作者: <?php echo htmlspecialchars($news['author']); ?> ·
    分类: <?php echo htmlspecialchars($news['category']); ?> ·
    浏览: <?php echo htmlspecialchars($news['views']); ?> ·
    <?php echo htmlspecialchars($news['create_time']); ?>
  </div>
  <p style="line-height:1.8;"><?php echo htmlspecialchars($news['content']); ?></p>
</div>

<div class="card">
  <h2>💬 评论区</h2>
  <?php
  $news_id_int = (int)$id; // 评论区查询用了强制转换,这里没有漏洞,方便对比
  $comment_sql = "SELECT * FROM comments WHERE news_id=$news_id_int ORDER BY id DESC";
  $comment_result = mysqli_query($conn, $comment_sql);
  while ($c = mysqli_fetch_assoc($comment_result)):
  ?>
  <div style="border-bottom:1px solid #eee;padding:10px 0;">
    <b><?php echo htmlspecialchars($c['nickname']); ?>:</b>
    <!-- 漏洞点: 存储型XSS,评论内容直接输出,没有做htmlspecialchars转义 -->
    <span><?php echo $c['content']; ?></span>
  </div>
  <?php endwhile; ?>

  <form method="post" action="comment.php">
    <input type="hidden" name="news_id" value="<?php echo $news_id_int; ?>">
    <label>昵称</label>
    <input type="text" name="nickname" placeholder="请输入昵称">
    <label>评论内容</label>
    <textarea name="content" rows="3" placeholder="说点什么吧"></textarea>
    <button class="btn" type="submit">发表评论</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
