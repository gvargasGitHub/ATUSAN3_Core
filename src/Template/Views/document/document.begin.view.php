<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $_ENV['APP_TITLE'] ?></title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <!-- Framework CSS -->
  <?= self::writeCSS() ?>

  <!-- Framework JS -->
  <?= self::writeJS() ?>
</head>

<body>
  <div class="ats-loader-overlay" id="ats-loader">
    <div class="ats-loader-indicator"></div>
  </div>
  <?php require 'module.begin.view.php'; ?>