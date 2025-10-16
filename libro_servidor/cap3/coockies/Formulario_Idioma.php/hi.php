<?php
// hi.php - English page
// If no 'lang' cookie, set to 'en' by default
if (!isset($_COOKIE['lang'])) {
    setcookie('lang', 'en', time() + 30 * 24 * 60 * 60, '/');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Hi</title>
</head>
<body>
  <p>hi</p>
  <p><a href="idioma_form.php">Change language</a></p>
</body>
</html>
