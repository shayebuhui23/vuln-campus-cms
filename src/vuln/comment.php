<?php
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news_id = (int)$_POST['news_id'];
    $nickname = $_POST['nickname'];
    $content = $_POST['content'];

    /*
     * ============================================================
     * 漏洞点：存储型XSS
     * ------------------------------------------------------------
     * nickname和content直接存入数据库,没有做任何过滤,
     * 显示的时候(news.php里)也没有做htmlspecialchars转义输出。
     *
     * 攻击示例: 评论内容填入
     * <script>alert('XSS')</script>
     * 提交后,任何访问这条新闻的人都会执行这段脚本。
     *
     * 这里用了mysqli_real_escape_string,所以不存在SQL注入,
     * 但完全没有处理XSS问题,这是两类不同漏洞的对比:
     * SQL注入防的是"数据库层面",XSS防的是"浏览器渲染层面",
     * 两者需要分别处理,一个的修复不能替代另一个。
     * ============================================================
     */
    $nickname_safe = mysqli_real_escape_string($conn, $nickname);
    $content_safe = mysqli_real_escape_string($conn, $content);

    $sql = "INSERT INTO comments (news_id, nickname, content) VALUES ($news_id, '$nickname_safe', '$content_safe')";
    mysqli_query($conn, $sql);
}

header('Location: news.php?id=' . (isset($_POST['news_id']) ? (int)$_POST['news_id'] : 1));
exit;
