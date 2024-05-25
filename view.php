<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analiz Sonuçları</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $id = basename($_GET['id']); 
                            $file_path = "data/$id.json";

                            if (file_exists($file_path)) {
                                $json_data = file_get_contents($file_path);
                                $result = json_decode($json_data, true);

                                if ($result) {
                                    echo "<div class='results'>";
                                    echo "<h2>Analiz Sonuçları</h2>";
                                    echo "<p><strong>URL:</strong> " . htmlspecialchars($result['url']) . "</p>";
                                    echo "<p><strong>Yükleme Süresi:</strong> " . $result['total_time'] . " saniye</p>";
                                    echo "<p><strong>İlk Bayt Süresi:</strong> " . $result['starttransfer_time'] . " saniye</p>";
                                    echo "<p><strong>Sayfa Boyutu:</strong> " . $result['content_size'] . " KB</p>";
                                    echo "<p><strong>CSS Dosya Sayısı:</strong> " . $result['num_css'] . "</p>";
                                    echo "<p><strong>JS Dosya Sayısı:</strong> " . $result['num_js'] . "</p>";
                                    echo "<p><strong>Resim Sayısı:</strong> " . $result['num_images'] . "</p>";
                                    echo "</div>";
                                } else {
                                    echo "<p class='error alert alert-danger'>Veriler çözümlenemedi. JSON formatında bir hata olabilir.</p>";
                                }
                            } else {
                                echo "<p class='error alert alert-danger'>Geçersiz ID veya veri bulunamadı.</p>";
                            }
                        } else {
                            echo "<p class='error alert alert-danger'>ID parametresi eksik.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
