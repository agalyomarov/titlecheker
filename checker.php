<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
</head>

<body>
   <?php
   ini_set('log_errors', 'On');
   ini_set('error_log', 'php_errors.log');
   ini_set('allow_url_fopen', 1);
   ini_set("max_execution_time", "900");
   require "vendor/autoload.php";
   $client = new GuzzleHttp\Client();
   $urlsfile = 'url.txt';
   $resultfile = 'result.txt';
   $lines = file($urlsfile);
   file_put_contents($resultfile, '');
   foreach ($lines as $text) {
      $titlestart = stripos($text, '<title>');
      $titleend = stripos($text, '</title>');
      $title = mb_strcut($text, $titlestart + 7, $titleend - $titlestart - 7);
      $urlend = strripos(mb_strcut($text, 0, $titlestart), '-');
      $url = trim(mb_strcut($text, 0, $urlend));
      try {
         $content = $client->get($url, ['verify' => true]);
         $content = $content->getBody();
         file_put_contents('content.txt', $content);
      } catch (\Exception $e) {
         $content = NULL;
         file_put_contents('content.txt', '');
      }
      if ($content) {
         $contenttitlestart = stripos($content, '<title>');
         $contenttitleend = stripos($content, '</title>');
         $contenttitle = mb_strcut($content, $contenttitlestart + 7, $contenttitleend - $contenttitlestart - 7);
         $result = file_get_contents('result.txt');
         if (trim($title) == trim($contenttitle)) {
            $result .= $url . ' - ' . $contenttitle . ' - <font color="green">Равно</font><br><br>';
            file_put_contents('result.txt', $result);
         } else {
            $result .=  $url . ' - ' . $contenttitle . ' - <font color="red">Не равно</font><br><br>';
            file_put_contents('result.txt', $result);
         }
      } else {
         $result = file_get_contents('result.txt');
         $result .= $url . ' - ' . $title . ' - <font color="red">URL не корректный</font><br><br>';
         file_put_contents('result.txt', $result);
      }
   }
   echo file_get_contents('result.txt');
   ?>
</body>

</html>