<?php
session_start();

drive();

 function drive(){
$url_array = explode('?', 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$url = $url_array[0];

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';
$client = new Google_Client();
$client->setClientId('645773228886-9o6tvdiqtvi73rbu3i45enii7ip5bfuj.apps.googleusercontent.com');
$client->setClientSecret('iuIIVeXtaTStigpEjzOgLTgn');
$client->setRedirectUri($url);
$client->setScopes(array('https://www.googleapis.com/auth/drive'));
if (isset($_GET['code'])) {
    $_SESSION['accessToken'] = $client->authenticate($_GET['code']);
    header('location:'.$url);exit;
} elseif (!isset($_SESSION['accessToken'])) {
    $client->authenticate();
}
$files= array();
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$dir = dir('C:/Users/AI-CABIN/Downloads');
while ($file = $dir->read()) {
    if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
        $files[] = $file;
    }
}
$dir->close();

        $client->setAccessToken($_SESSION['accessToken']);
        $service = new Google_DriveService($client);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file = new Google_DriveFile();
        
        foreach ($files as $file_name) {
         
            $file_path = 'C:/Users/AI-CABIN/Downloads/'.$file_name;
            $mime_type = finfo_file($finfo, $file_path);
            $file->setTitle($file_name);
            $file->setDescription('This is a '.$mime_type.' document');
            $file->setMimeType($mime_type);
            $service->files->insert(
                $file,
                array(
                    'data' => file_get_contents($file_path),
                    'mimeType' => $mime_type
                )
            );
            unlink($file_path);
        
        }
        finfo_close($finfo);
        header('location: index.html');exit;
 
 }
?>