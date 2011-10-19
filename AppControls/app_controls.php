<?php
   class AppControlsComponent extends Object {
      function empty_dir($dir, $delete_me = false){
         $do_not_delete = array('.', '..');
         //Check to see if we can open the dir
         if (!$dh = @opendir($dir)) return false;

         //Loop through dir and remove items
         while(false !== ($obj = readdir($dh))){
            if (in_array($obj, $do_not_delete)) continue;
            //Remove inner dirs / files
            if (!@unlink($dir . '/' . $obj)) $this->empty_dir($dir . '/' . $obj, true);
         }

         //Clsoe dir
         closedir($dh);

         //Should we remove the dir?
         if ($delete_me) @rmdir($dir);

         return true;
      }

      function read_file_contents($file){
         if (!is_file($file) || !is_readable($file)) {
            return false;
         }

         $contents = file_get_contents($file);

         return $contents;
      }

      function recursive_copy($src, $dest){
         $no_copy_files = array('.', '..', 'preview', '.svn', '.git');
         $dir = opendir($src);
          @mkdir($dest);
          while(false !== ( $file = readdir($dir)) ) {
              if ( !in_array($file, $no_copy_files) && substr($file, -4) !== '.swp') {
                  if ( is_dir($src . '/' . $file) ) {
                      $this->recursive_copy($src . '/' . $file,$dest . '/' . $file);
                  }
                  else {
                      copy($src . '/' . $file,$dest . '/' . $file);
                  }
              }
          }
          closedir($dir);

          return true;
      }

      function chmodr($path, $filemode){
         if (!is_dir($path))
            return chmod($path, $filemode);

         $dh = opendir($path);
         while (($file = readdir($dh)) !== false){
            if($file != '.' && $file != '..'){
               $fullpath = $path.'/'.$file;
               if(is_link($fullpath))
                  return FALSE;
               elseif(!is_dir($fullpath) && !chmod($fullpath, $filemode))
                  return FALSE;
               elseif(!chmodr($fullpath, $filemode))
                  return FALSE;
            }
         }
         closedir($dh);

         if(chmod($path, $filemode))
            return TRUE;
         else
            return FALSE;
      }

      function chownr($path, $owner){
         if (!is_dir($path))
            return chown($path, $owner);

         $dh = opendir($path);
         while (($file = readdir($dh)) !== false){
            if($file != '.' && $file != '..'){
               $fullpath = $path.'/'.$file;
               if(is_link($fullpath))
                  return FALSE;
               elseif(!is_dir($fullpath) && !chown($fullpath, $owner))
                  return FALSE;
               elseif(!chownr($fullpath, $owner))
                  return FALSE;
            }
         }

         closedir($dh);

         if(chown($path, $owner))
            return TRUE;
         else
            return FALSE;
      }

      function chgrpr($path, $group){
         if (!is_dir($path))
            return chgrp($path, $group);

         $dh = opendir($path);
         while (($file = readdir($dh)) !== false){
            if($file != '.' && $file != '..'){
               $fullpath = $path.'/'.$file;
               if(is_link($fullpath))
                  return FALSE;
               elseif(!is_dir($fullpath) && !chgrp($fullpath, $group))
                  return FALSE;
               elseif(!chgrpr($fullpath, $group))
                  return FALSE;
            }
         }

         closedir($dh);

         if(chgrp($path, $group))
            return TRUE;
         else
            return FALSE;
      }

      function replace_content($start, $end, $new, $source){
         //The ? after the * makes the regex capture the first occurence rather than the last.
         return preg_replace('#('.preg_quote($start).')(.*?)('.preg_quote($end).')#si', '$1'.$new.'$3', $source);
      }

     function create_dir($root, $dir_name, $overwrite = false){
         $dir_name = trim($dir_name);
         $dir = $root . $dir_name;
         $dir_created = false;

         //Does the dir exist?
         $dir_exists = file_exists($dir);

         //If the dir does nto exist
         if (!$dir_exists){
            //Create it
            $dir_created = mkdir($dir, 0777);
         }else{
            if ($overwrite){
               return true;   //DIR EXISTS
            }else{
               $dir_created = true;
            }
         }

         if (!$dir_created) return false; //MKDIR failed

         return true;
      }

     function unzip_archive($archive, $dest){
         $dir = $dest;
         $ROOT = Configure::read('ROOT');
         //Require the PCLZip class
         require_once $ROOT . "/vendors/pclzip.lib.php";

         //Create a new Archive object - PCLZip - and give it archive name as arg
         $Archive = new PCLZip($archive);

         //Attempt tp extract template
         if (($v_result_list = $Archive->extract(PCLZIP_OPT_PATH, $dir)) == 0){
            $e_string = "$archive == $dest --> " . $Archive->errorInfo(true);
            return $Archive->errorInfo(true);
         }else{
            $e_string = "$archive == $dest -> 000";
            return true;
         }
      }

		/**
		 * Prevent File caching by reading the contents of $file and replacing $tag with "$tag?[PHP TIME]"
		 * 
		 * Ex: 
		 * tag: style.css
		 * output in file: style.css?923908
		 * 
		 * @param string $file
		 * @param string $tag
		*/
      function prevent_file_caching($file, $tag){
         //Read file
         if (is_file($file) and is_readable($file)){
            $file_contents = file_get_contents($file);
         }else{
            return false;
         }

         //Replace tag
         $file_contents = @str_replace("$tag", "$tag?" . time(), $file_contents);

         //Rewrite file
         file_put_contents($file, $file_contents);

         return true;
      }

      function generate_unique_id(){
         $rid = '';

         //Create a 16 character random string of numbers.
         for ($i = 0; $i < 16; $i++){
            $rid .= mt_rand(0, 9);
         }

         //Send back the MD5 of this string
         return md5($rid);
      }

		function format_decimal($value){
			return number_format($value, 2, '.', ' ');
		}
}
