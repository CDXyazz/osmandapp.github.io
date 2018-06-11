<?php

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function loadUpdatesFromDir($output, $outputIndexes, $dir, $fileFilter, $timestamp){
  if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
      while (($file = readdir($dh)) !== false) {
        if(startsWith(strtolower($file), strtolower($fileFilter))) {
            $indexName=$file;
            $filename = $dir . $file ; //"./test112.zip";
            $size =  number_format((filesize($filename) / (1024.0*1024.0)), 1, '.', '');
            $containerSize = filesize($filename);
            $contentSize = filesize($filename);
            $date = date('d.m.Y',filemtime($filename));
            $timestampF = filemtime($filename);
            if($timestampF > intval(substr($timestamp, 0, -3))) {
              $out = $output->createElement('update');
            } else {
              $out = $output->createElement('outdate');
            }
              $outputIndexes->appendChild($out);
              $out -> setAttribute("updateDate", substr($file, -strlen('.obf.gz') - 8, -strlen('.obf.gz')));
              $out -> setAttribute("containerSize", $containerSize);
              $out -> setAttribute("contentSize", $contentSize);
              $out -> setAttribute("timestamp", $timestampF * 1000);
              $out -> setAttribute("date", $date);
              $out -> setAttribute("size", $size);
              $out -> setAttribute("name", $indexName);
            
        }
      }
      closedir($dh);
    }
  } else {
    print($dir . " not a directory!\n");
  }
}


if(!isset($_GET['file'])  || !isset($_GET['timestamp']) ) {
   header('HTTP/1.0 404 Not Found');
   die(1);
 }



$dom = new DomDocument();
$output = new DOMDocument();
$output->formatOutput = true;
$outputIndexes = $output->createElement( "updates" );
$outputIndexes->setAttribute('file', $_GET['file']);
$outputIndexes->setAttribute('timestamp', $_GET['timestamp']);
$output->appendChild( $outputIndexes );
//if(isset($_GET['aosmc'])) {
  $folder = strtolower($_GET['file']);
  loadUpdatesFromDir($output, $outputIndexes, 'aosmc/'.$folder.'/', $_GET['file'], $_GET['timestamp']);
//} else {
//  loadUpdatesFromDir($output, $outputIndexes, 'osmc/', $_GET['file'], $_GET['timestamp']);
//}

echo $output->saveXML();   
?>
