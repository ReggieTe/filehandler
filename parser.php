<?php 
ini_set('xdebug.max_nesting_level', 99999);
include "FileHandler.php";

$findMe=$argv[0];
$filesDir=$argv[1];

if(empty($findMe))
{
  echo "Search key is not set";
  exit;
}

if(empty($filesDir))
{
  echo "Search directory is not set";
  exit;
}


processFiles($filesDir);

fileHandler::parseDir($findMe,fileHandler::getDirContents($filesDir));

function processFiles($files)
{  if(is_dir($files))
    { $files=fileHandler::getDirContents($files);       
        foreach($files as $file)
        {        
            if(fileHandler::fileExt($file)=="zip")
            {
                fileHandler::extractZip($file);
            }  
            if(fileHandler::fileExt($file)=="gz")
            {
                fileHandler::extractGz($file);
            }  
            if(fileHandler::fileExt($file)=="tar")
            {
                fileHandler::extractTar($file);
            } 
            if(fileHandler::fileExt($file)=="tar.gz")
            {
                fileHandler::extractTarGz($file);
            }               
        }        
    }   
   
}


