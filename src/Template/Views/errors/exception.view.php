<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= APP_NAME ?> Report</title>
  <style>
    <?= file_get_contents(APP_CORE . '/src/Statics/css/alerts.css') ?>
  </style>
</head>

<body>
  <h3><?= APP_NAME ?> Report:</h3>
  <div class="alert error">
    <strong>Exception: </strong><?= $message ?>
    <pre><?= isset($detail) ? $detail : '' ?></pre>
  </div>
</body>

</html>