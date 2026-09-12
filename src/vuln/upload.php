<?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ?> 的文件,
         * 命名为 shell.php,上传成功后直接访问
         * http://目标地址/uploads/shell.php
         * 就能通过POST传入cmd参数执行任意PHP代码(俗称"一句话木马")
         * ============================================================
         */
        $upload_dir = __DIR__ . '/uploads/';
        $filename = $file['name']; // 直接用用户提供的原始文件名,没有做任何处理
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $message = '上传成功！文件保存路径: /uploads/' . $filename;
            $message_type = 'success';
            $uploaded_path = 'uploads/' . $filename;
        } else {
            $message = '上传失败';
            $message_type = 'error';
        }
    } else {
        $message = '上传出错，错误代码: ' . $file['error'];
        $message_type = 'error';
    }
}

$page_title = '上传头像';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:500px;">
  <h2>📤 上传个人头像</h2>
  <p style="font-size:13px;color:#999;">支持jpg/png格式图片，用于个人资料展示</p>

  <?php if ($message): ?>
    <div class="flash <?php echo $message_type; ?>">
      <?php echo $message; ?>
      <?php if ($uploaded_path): ?>
        <br><a href="<?php echo htmlspecialchars($uploaded_path); ?>" target="_blank">点击查看上传的文件</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>选择文件</label>
    <input type="file" name="avatar">
    <button class="btn" type="submit" style="margin-top:10px;">上传</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
