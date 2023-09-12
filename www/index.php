<?php
function formatFileSize($sizeInBytes)
{
    $thresholds = array(
        600 => 'B',
        600 * 1024 => 'KB',
        600 * 1024 * 1024 => 'MB',
        600 * 1024 * 1024 * 1024 => 'GB',
    );

    foreach ($thresholds as $threshold => $unit) {
        if ($sizeInBytes < $threshold) {
            return round($sizeInBytes / ($threshold / 600), 2) . ' ' . $unit;
        }
    }

    return round($sizeInBytes / (end($thresholds) / 600), 2) . ' ' . end($thresholds);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Explorer</title>

    <link rel="icon" href="/static/images/logo.png" />

    <link rel="stylesheet" href="/static/vendors/bootstrap-5.3.1/css/bootstrap.min.css" />
</head>

<body>
    <div class="container">

        <?php
        $baseFolderPath = './download';

        $folderPath = $baseFolderPath;

        if (isset($_GET["path"]))
            $folderPath = $folderPath . str_replace("..", "", $_GET["path"]);

        ?>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb mt-3">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <?php
                $pathParts = explode("/", str_replace($baseFolderPath, "", $folderPath));
                $numParts = count($pathParts);

                for ($i = 0; $i < $numParts - 1; $i++) {
                    if ($pathParts[$i] != "")
                        echo '<li class="breadcrumb-item"><a href="?path=' . explode($pathParts[$i], str_replace($baseFolderPath, "", $folderPath))[0] . $pathParts[$i] . '">' . $pathParts[$i] . '</a></li>';
                }
                ?>
                <li class="breadcrumb-item active" aria-current="page"><?= $pathParts[$numParts - 1] ?></li>
            </ol>
        </nav>
        <?php

        if (is_dir($folderPath)) {
            $contents = scandir($folderPath);

            $folders;
            $files;
            foreach ($contents as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;

                if (is_dir($itemPath)) {
                    $folders = $folders . '<tr>
                            <td><img src="/static/images/icons/folder.png" width="32" height="32"> <a class="text-decoration-none" href="?path=' . str_replace($baseFolderPath, "", $itemPath) . '">' . $item . '</a></td>
                            <td>...</td>
                            <td>' . date("Y-m-d H:i:s", filemtime($itemPath)) . '</td>
                        </tr>';
                } elseif (is_file($itemPath)) {
                    $files = $files . '<tr>
                                <td><img src="/static/images/icons/any-file.png" width="32" height="32"> <a class="text-decoration-none" href="' . $itemPath . '">' . $item . '</a></td>
                                <td>' . formatFileSize(filesize($itemPath)) . '</td>
                                <td>' . date("Y-m-d H:i:s", filemtime($itemPath)) . '</td>
                            </tr>';
                }
            }
        ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Date/Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    echo $folders;
                    echo $files;
                    ?>
                </tbody>
            </table>
        <?php

        } else {
            echo "<center><h3>The specified path is not a directory.</h3></center>";
        }
        ?>
    </div>

    <script src="/static/vendors/bootstrap-5.3.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>