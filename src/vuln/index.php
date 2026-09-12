<?php
require_once __DIR__ . '/includes/db.php';

$sql = "SELECT id, title, author, category, views, create_time FROM news ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$page_title = '首页';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h2>📰 最新校园新闻</h2>
  <?php while ($news = mysqli_fetch_assoc($result)): ?>
  <div class="news-item">
    <a href="news.php?id=<?php echo $news['id']; ?>"><?php echo htmlspecialchars($news['title']); ?></a>
    <div class="meta">
      作者: <?php echo htmlspecialchars($news['author']); ?> ·
      分类: <?php echo htmlspecialchars($news['category']); ?> ·
      浏览: <?php echo $news['views']; ?> ·
      <?php echo $news['create_time']; ?>
    </div>
  </div>
  <?php endwhile; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
