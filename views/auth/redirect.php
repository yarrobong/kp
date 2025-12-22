<div class="page-header">
    <h1>Требуется авторизация</h1>
</div>

<div class="user-section">
    <div class="empty-state">
        <h2><?php echo htmlspecialchars($message); ?></h2>
        <p>Вы будете автоматически перенаправлены на страницу входа через 3 секунды.</p>
        <p>Если перенаправление не сработало, <a href="<?php echo htmlspecialchars($redirectUrl); ?>">нажмите здесь</a>.</p>
    </div>
</div>

<script>
    setTimeout(function() {
        window.location.href = '<?php echo htmlspecialchars($redirectUrl); ?>';
    }, 3000);
</script>
