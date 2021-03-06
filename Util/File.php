<?php
/**
* @author 马涛 <matao@bj.tom.com>
* @version v 1.0 2004/04/16
* @package Core_Class
*/

/**
* File 基本文件IO类
*/

class KISS_Util_File {
    /**
    * 值：0; 没有错误发生，文件上传成功。
    */
    const UPLOAD_ERR_OK = UPLOAD_ERR_OK;
    /**
    * 值：1; 上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。
    */
    const UPLOAD_ERR_INI_SIZE = UPLOAD_ERR_INI_SIZE;
    /**
    * 值：2; 上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
    */
    const UPLOAD_ERR_FORM_SIZE = UPLOAD_ERR_FORM_SIZE;
    /**
    * 值：3; 文件只有部分被上传。
    */
    const UPLOAD_ERR_PARTIAL = UPLOAD_ERR_PARTIAL;
    /**
    * 值：4; 没有文件被上传。
    */
    const UPLOAD_ERR_NO_FILE = UPLOAD_ERR_NO_FILE;
    /**
    * 值：5; 上传文件类型不对。
    */
    const UPLOAD_ERR_TYPE = 5;
    /**
    * 值：6; 上传文件扩展名不对。
    */
    const UPLOAD_ERR_EXTEND = 6;
    /**
    * 值：7; 上传文件的大小超过了用户调用参数指定的值。
    */
    const UPLOAD_ERR_USER_SIZE = 7;
    /**
    * 值：8; 其他错误。
    */
    const UPLOAD_ERR_OTHER = 8;
    /**
    * 值：9; 空文件。
    */
    const UPLOAD_ERR_ZERO_SIZE = 9;

    function uploadFile ($pFile, $pFileName = "", $pPath="") {
        $pFileName = $_FILES[$pFile]['name'];
        $pFile     = $_FILES[$pFile]['tmp_name'];
        if($pFileName != "") {
            $filename = explode(".",$pFileName);
            $filesize = filesize($pFile);
            $path = FILE_PATH.$pPath;
            
            set_time_limit(0);
            $time_now = date ("His");
            $time_sec = microtime();
            $timestemp = $time_now.$time_sec[2].$time_sec[3].$time_sec[4].$time_sec[5].$time_sec[6];
            $newFileName = "{$timestemp}{$pFile_name}.{$filename[1]}";
            
            if(copy ($pFile, $path.$newFileName)) {
                chmod ($path.$newFileName, 0644);
                return $newFileName;
            }
            else {
                return "";
            }
        }
        else {
            return "";
        }
    }
    
    static function allUpload ($pPath="", $pExtendName = array(),  $pFileType = array(), $pMaxFileSize = 0) {
        $return = 0;
        if(count($_FILES)>0) {
            $keys = array_keys($_FILES);
            $length = count($keys);
            for($i =0; $i<$length; $i++) {
                $current_upload_file = &$_FILES[$keys[$i]];
                if ($current_upload_file['error'] == self::UPLOAD_ERR_OK ) {
                    if ($pMaxFileSize > 0 && $current_upload_file['size'] > $pMaxFileSize) {
                        $current_upload_file['error'] = self::UPLOAD_ERR_USER_SIZE;
                        continue;
                    }
                    if (count($pFileType) > 0 && !in_array($current_upload_file['type'], $pFileType)) {
                        $current_upload_file['error'] = self::UPLOAD_ERR_TYPE;
                        continue;
                    }
                    $pos = strrpos($current_upload_file['name'], ".");
                    if ($pos > 0) {
                        $current_upload_file['extend'] = strtolower(substr($current_upload_file['name'],$pos+1));
                        $current_upload_file['caption'] = substr($current_upload_file['name'],0,strlen($current_upload_file['name'])-strlen($current_upload_file['extend'])-1);
                    }
                    else {
                        $current_upload_file['extend'] = '';
                        $current_upload_file['caption'] = $current_upload_file['name'];
                    }
                    if (count($pExtendName) > 0 && !in_array($current_upload_file['extend'], $pExtendName)) {
                        $current_upload_file['error'] = self::UPLOAD_ERR_EXTEND;
                        continue;
                    }
                    $current_upload_file['new_file'] = md5_file($current_upload_file['tmp_name']);
                    if(rename($current_upload_file['tmp_name'], "{$pPath}{$current_upload_file['new_file']}")) {
                        chmod ("{$pPath}{$current_upload_file['new_file']}", 0644);
                    }
                    $return ++;
                }
                elseif ($current_upload_file['error'] == self::UPLOAD_ERR_NO_FILE) {
                    unset($_FILES[$keys[$i]]);
                }
            }
        }
        return $return;
    }
    
    function removeFile($pFileName) {
        if($pFileName=="")
        return true;
        if(!is_file($pFileName))
        return true;
        if(unlink ($pFileName)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    function writeFile($pFilename, $pContent) {
        // 首先我们要确定文件存在并且可写。
        if (!file_exists($pFilename) || is_writable($pFilename)) {
            if (!$handle = fopen($pFilename, 'w')) {
                return false;
            }
            
            // 将$pContent写入到我们打开的文件中。
            if (!fwrite($handle, $pContent)) {
                return false;
            }
            fclose($handle);
            chmod ($pFilename, 0644);
            return true;
        }
        else {
            return false;
        }
    }
}
?>
