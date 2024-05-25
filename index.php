<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Hız Analiz Aracı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h1 class="card-title text-center">Website Hız Analiz Aracı</h1>
                        <form id="analyzeForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="url" class="form-label">Web Sitesi URL'si:</label>
                                <input type="text" id="url" name="url" class="form-control" placeholder="https://example.com" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i>  Analiz Et</button>
                            </div>
                        </form>
                        <div id="loading" class="text-center mt-4" style="display: none;">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                            <p>Lütfen bekleyin, analiz yapılıyor...</p>
                        </div>
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $url = filter_var($_POST["url"], FILTER_SANITIZE_URL);

                            if (filter_var($url, FILTER_VALIDATE_URL)) {
                                $start_time = microtime(true);

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_HEADER, 1);
                                $response = curl_exec($ch);

                                if (!curl_errno($ch)) {
                                    $end_time = microtime(true);
                                    $total_time = $end_time - $start_time;

                                    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                                    $header = substr($response, 0, $header_size);
                                    $body = substr($response, $header_size);

                                    $info = curl_getinfo($ch);

                                    curl_close($ch);

                                    $content_size = strlen($body);
                                    $num_css = preg_match_all('/<link.*?rel=["\']stylesheet["\']/', $body, $matches);
                                    $num_js = preg_match_all('/<script.*?src=["\'].*?["\']/', $body, $matches);
                                    $num_images = preg_match_all('/<img.*?src=["\'].*?["\']/', $body, $matches);

                                    $result = [
                                        'url' => $url,
                                        'total_time' => number_format($total_time, 2),
                                        'starttransfer_time' => number_format($info['starttransfer_time'], 2),
                                        'content_size' => number_format($content_size / 1024, 2),
                                        'num_css' => $num_css,
                                        'num_js' => $num_js,
                                        'num_images' => $num_images
                                    ];

                                    $unique_id = uniqid();
                                    $json_data = json_encode($result, JSON_PRETTY_PRINT);
                                    file_put_contents("data/$unique_id.json", $json_data);
                                    include 'config.php';
                                    $share_link = "$domain/view.php?id=$unique_id";
                                    echo "<div class='results mt-4'>";
                                    echo "<h2><i class='fas fa-chart-bar'></i> Analiz Sonuçları</h2>";
                                    echo "<p><strong><i class='fas fa-clock'></i> Yükleme Süresi:</strong> " . $result['total_time'] . " saniye</p>";
                                    echo "<p><strong><i class='fas fa-bolt'></i> İlk Bayt Süresi:</strong> " . $result['starttransfer_time'] . " saniye</p>";
                                    echo "<p><strong><i class='fas fa-file'></i> Sayfa Boyutu:</strong> " . $result['content_size'] . " KB</p>";
                                    echo "<p><strong><i class='fas fa-file-code'></i> CSS Dosya Sayısı:</strong> " . $result['num_css'] . "</p>";
                                    echo "<p><strong><i class='fas fa-code'></i> JS Dosya Sayısı:</strong> " . $result['num_js'] . "</p>";
                                    echo "<p><strong><i class='fas fa-image'></i> Resim Sayısı:</strong> " . $result['num_images'] . "</p>";
                                    echo "<p><strong><i class='fas fa-share'></i> Paylaşım Linki:</strong> <a href='$share_link'>$share_link</a></p>";
                                    echo "</div>";
                                } else {
                                    echo "<p class='error alert alert-danger'><i class='fas fa-exclamation-circle'></i> Geçersiz URL veya siteye erişilemiyor.</p>";
                                }
                            } else {
                                echo "<p class='error alert alert-danger'><i class='fas fa-exclamation-circle'></i> Lütfen geçerli bir URL girin.</p>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
