<?php
$_REQUEST[folder]="ricons32x32";
$run= full_rmdir($_REQUEST[folder]);
$dirname="ricons32x32";
$run= full_rmdir($dirname);
echo">>>>$run<<<";
function full_rmdir($dirname){
        if ($dirHandle = opendir($dirname)){
            $old_cwd = getcwd();
            chdir($dirname);

            while ($file = readdir($dirHandle)){
                if ($file == '.' || $file == '..') continue;

                if (is_dir($file)){
                    if (!full_rmdir($file)) return false;
                }else{
                    if (!unlink($file)) return false;
                }
            }

            closedir($dirHandle);
            chdir($old_cwd);
            if (!rmdir($dirname)) return false;

            return true;
        }else{
            return false;
        }
 }


?>