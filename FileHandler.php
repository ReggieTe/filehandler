<?php
/**
 * Description of FileHandler
 * This class is for handle all the files operations
 * Anything to do with file handling must define in this class
 *
 * @author Reggie Te
 * @copyright (c) 2016, Reggie Te
 * @version 1.0
 * Issued UNDER the MIT License

Copyright (c) 2016 ReggieTe

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 */
class FileHandler {
/**
 * 
 * @param bytes $fileContent   file bytes to be upload to the server
 * @param string $filename    name of the file being uploaded
 * @return boolean     to reflect whether the upload was successful or not
 */
    public static function uploadContent($fileContent = null, $filename = null) {
        if ($fileContent != null && $filename != null) {
            if (is_uploaded_file($fileContent)) {

                return copy($fileContent, $filename);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public static function getDirContents($dir, &$results = array()){
        if(is_dir($dir))
        { 
        $files = scandir($dir);
    
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                self::getDirContents($path, $results);
                $results[] = $path;
            }
        }}
    
        return $results;
    }
    
      
    
    public static function fileExt($path=null){
        if ($path != null) {    
        $extension =pathinfo($path);            
        return array_key_exists("extension",$extension) ?$extension['extension']: false;          

        } else {
          return false;
        }
    }

    public static function getContent($path=null){
        if ($path != null) {
            return is_file($path)? file_get_contents($path) : false;            

        } else {
            return false;
        }
    }


    public static function strpos_recursive($haystack, $needle, $offset = 0, &$results = array()) {               
        $offset = strpos($haystack, $needle, $offset);
        if($offset === false) {
            return $results;           
        } else {
            $results[] = $offset;
            return self::strpos_recursive($haystack, $needle, ($offset + 1), $results);
        }
    }
/**
 * @
 * 
 */
    public static function parseDir($findMe=null ,$files=array(),$plentyData=false){
        /*
        Potential Error :Fatal error: Maximum function nesting level of '256' reached, aborting! 
            Fix :  ini_set('xdebug.max_nesting_level', 9999);
            */
//$files=array();        
//if(is_dir($filesDir)&&$findMe!=null)
       // {
            //$files= FileHandler::dirContent($filesDir);
            
            foreach($files as $file)
            { 
                //$file=$filesDir."/".$file;
                if(is_file($file))
                {           
                    echo "Searching : $file  for  '$findMe' \n";
                    //load file and parser it for keyword
                    $result=FileHandler::getContent($file);
                    $found = FileHandler::strpos_recursive($result, $findMe);               
                    if($found) {
                        if($plentyData){
                            foreach($found as $pos) {
                                echo '+ Found "'.$findMe.'" in string "'.$file.'" at position <b>'.$pos.'</b><br />'."\n";
                            }
                        }
                        else
                        {
                            echo "+ Found '$findMe'  in $file\n\n";
                        }
                            
                   } else {
                       echo "- Not Found'$findMe' in $file\n\n";
                   }
    
           }

       //}
     
    }
    echo "Completed\n";
    } 
/**
 * 
 * @param string $path  path on the server where to create directory
 * @return boolean
 */
    public static function createDir($path = null) {

        if ($path != null) {
            return !is_dir($path) ? mkdir($path) : false;
        } else {
            return false;
        }
    }
/**
 * 
 * @param string $path  path where the content to be deleted resides
 * @return boolean
 */
    public static function iterateDir($path = null) {
       
        $files=array();
       $dir=array();
        if ($path != null&&is_dir($path)) {
            foreach (self::dirContent($path) as $name => $stats) {

                if (array_search('.', str_split($name))) {
                    //add file
                   array_push($files,"$path/$name");
                } else {
                    //add directory
                    array_push($dir,"$path/$name");
                }
            }

            return ["files"=>$files,"dir"=>$dir];
        } else {
            return false;
        }
    }


public static function extractZip($file=null)
{
            if(is_file($file)){
                // get the absolute path to $file
                $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
                $zip = new ZipArchive;
                $res = $zip->open($file);
                if ($res === TRUE) {
                // extract it to the path we determined above
                $zip->extractTo($path);
                $zip->close();
                unlink($file);
                return true;
                } else {        
                return false;
                }
            }
            else{
                return false;
            }
        
}

public static function extractGz($file_name=null)
{
        //This input should be from somewhere else, hard-coded in this example
    if(is_file($file_name)){
        // Raising this value may increase performance
        $buffer_size = 4096; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $file_name);     
        // Open our files (in binary mode)
        $file = gzopen($file_name, 'rb');
        $out_file = fopen($out_file_name, 'wb');     
        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }    
        // Files are done, close files
        fclose($out_file);
        gzclose($file);
        unlink($file_name);
        return true;
    }
    else
    {
        return false;
    }
}

public static function extractTar($file=null)
{
       $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
        try {
            $phar = new PharData($file);
            $phar->extractTo($path); // extract all files
            unlink($file);
        } catch (Exception $e) {
            // handle errors
        }
}


public static function extractTarGz($file=null)
{
    
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
        try {
            $phar = new PharData($file);
            $phar->decompress(); // creates /path/to/my.tar

            $phar = new PharData($file);
            $phar->extractTo($path); // extract all files
            unlink($file);
        } catch (Exception $e) {
            // handle errors
      
    }
}

/**
 * 
 * @param string $path    file to delete from server
 * @return boolean
 */
    public static function deleteSingleFiles($path = null) {

        if ($path != null) {
            //delete file on provided path
            return is_file($path) ? unlink($path) : false;
        } else {
            return false;
        }
    }
/**
 * 
 * @param string $dir   directory to remove
 * @return boolean
 */
    public static function deleteSingleDir($dir = null) {
        if ($dir != null) {    //delete dir on provided path
            return is_dir($dir) ? rmdir($dir) : false;
        } else {
            return false;
        }
    }
/**
 * 
 * @param string $path path where the content to be diplayed resides
 * @return boolean
 */
    public static function dirContent($path = null) {
        if ($path != null) {
            // Define an array to hold the files
            $files = array();

            if(is_dir($path))
            {
// Open the current directory
            
            $d = ($path == null ? null :(is_dir($path)?dir($path):null) );

// Loop through all of the files:
            while (false !== ($file = $d->read())) {
                // If the file is not this file, and does not start with a '.' or '~'
                // and does not end in LCK, then store it for later display:
                if (($file{0} != '.') && ($file{0} != '~') && (substr($file, -3) != 'LCK')) {
                    // Store the filename, and full data from a stat() call:
                    $files[$file] = @stat($file);
                }
            }
// Close the directory
            $d->close();
// Sort the files so that they are alphabetical
            }
            ksort($files);

            return $files;
        } else {
            return false;
        }
    }
/**
 * 
 * @param string $dir  directory to clean
 * @param array $fileCodes   user valid file codes
 * @return boolean
 */
    public static function cleanDir($dir = null, $fileCodes = array()) {
        if ($dir != null && $fileCodes != null) {
            $avialableFiles = array();

            foreach (FileHandler::dirContent($dir) as $name => $stats) {
                array_push($avialableFiles, $name);
            }
            //retrive files that are hanging e.g directory with no record in the database
            foreach (array_diff($avialableFiles, $fileCodes) as $key => $value) {
                $path = $dir . "/" . $value;
                //delete files and sub directories
                FileHandler::iterateDir($path);
                //delete directory
                FileHandler::deleteSingleDir($path);
            }
            return true;
        } else {
            return false;
        }
    }
public static function createFolders($id=null) {
    $id==null?$id=Session::get('usercodeid'):$id=$id;
    
    if ($id != null) {
        $path = RELATIVEFILEPATH.$id;
        
        $userFilesToCreate = array(
            "main" => $path,
            "images" => $path . "/images",
            "content" => $path . "/content",
            "document" => $path . "/document"
        );
        foreach ($userFilesToCreate as $key => $value) {
            FileHandler::createDir($value);
        }
        return true;
    } else {
        return false;
    }
}
 
    public static function fileSize($bytes)
    {
     $display = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

    // Now, constantly divide the value by 1024 until it is less than 1024
    $level = 0;
    while ($bytes > 1024) {
        $bytes /= 1024;
        $level++;
    }

    // Now we have our final value, format it to just 1 decimal place
    // and append on to the the appropriate level moniker.
    return round($bytes, 6);   
    }
    public static function fileSizeWithTag($bytes)
    {
     $display = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

    // Now, constantly divide the value by 1024 until it is less than 1024
    $level = 0;
    while ($bytes > 1024) {
        $bytes /= 1024;
        $level++;
    }

    // Now we have our final value, format it to just 1 decimal place
    // and append on to the the appropriate level moniker.
    return round($bytes, 1) . ' ' . $display[$level];   
    }
}
