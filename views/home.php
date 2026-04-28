<?php /** @var array $data */ ?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($data['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        body { padding-top: 2rem; background-color: #f8f9fa; }
        .badge-open { color: #2e7d32; font-weight: 600; background: #e8f5e9; padding: 2px 8px; border-radius: 12px; font-size: 0.85em; }
        .badge-full { color: #c62828; font-weight: 600; background: #ffebee; padding: 2px 8px; border-radius: 12px; font-size: 0.85em; }
        .endpoint-card { margin-bottom: 1rem; padding: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; background: white; }
        .grid-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
        code { color: #d81b60; font-weight: bold; background: #f4f4f4; padding: 4px 8px; border-radius: 4px;}
    </style>
</head>
<body>
    <main class="container">
        <hgroup style="text-align: center; margin-bottom: 3rem;">
            <h1> <?= htmlspecialchars($data['title']) ?></h1>
            //<p>Môi trường: <strong><?= htmlspecialchars($data['app_env']) ?></strong> | Quản lý bởi: <strong><?= htmlspecialchars($data['organizer']) ?></strong></p>
        </hgroup>

        <h3>Danh sách thiết bị (Kho)</h3>
        <div class="grid-container">
            <?php foreach ($data['equipment'] as $item): ?>
                <article style="margin: 0;">
                    <header style="padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                        <strong><?= htmlspecialchars($item['name']) ?></strong>
                    </header>
                    <p style="margin-bottom: 0.5rem; font-size: 0.9em;">
                        Phân loại: <?= htmlspecialchars($item['category']) ?><br>
                        Vị trí: <?= htmlspecialchars($item['location']) ?><br>
                        Tổng số lượng: <?= htmlspecialchars((string)$item['total_units']) ?><br>
                        Sẵn sàng cho mượn: <strong><?= htmlspecialchars((string)$item['available_units']) ?></strong>
                    </p>
                    <footer style="padding-top: 0.5rem; margin-top: 0.5rem;">
                        Trạng thái: 
                        <?php if ($item['available_units'] > 0): ?>
                            <span class="badge-open">Sẵn sàng</span>
                        <?php else: ?>
                            <span class="badge-full">Hết hàng</span>
                        <?php endif; ?>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>

        <h3 style="margin-top: 3rem;">Tài liệu API (Endpoints)</h3>
        <div class="endpoint-card">
            <p><code>GET /equipment</code> — Lấy danh sách thiết bị định dạng JSON</p>
            <p><code>HEAD /equipment</code> — Kiểm tra header hệ thống</p>
            <p><code>POST /reservations</code> — Gửi dữ liệu tạo đặt mượn mới</p>
            <p><code>OPTIONS /reservations</code> — Xem các method được phép gọi</p>
            <p><code>GET /health</code> — Kiểm tra trạng thái máy chủ</p>
        </div>
    </main>
</body>
</html>
