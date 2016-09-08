<?php

namespace Lib\Core;

class Data{
    public static function read($filename){
        $filename = RUNTIME_DIR_DATA.$filename;
        if(file_exists($filename)){
            return file_get_contents($filename);
        }else{
            return FALSE;
        }
    }
    
    public static function read_json($filename, $assoc = true){
        $filename = RUNTIME_DIR_DATA.$filename;
        $raw = self::read($filename);
        if($raw === FALSE){
            return FALSE;
        }
        $ret =  json_decode($raw, $assoc);
        return $ret;
    }
    
    public static function write($filename, $content, $overwrite = TRUE){
        $filename = RUNTIME_DIR_DATA.$filename;
        if(!$overwrite && file_exists($filename)){
            return FALSE;
        }else{
            return file_put_contents($filename, $content);
        }
    }
    
    public static function write_json($filename, $content, $overwrite = TRUE){
        $filename = RUNTIME_DIR_DATA.$filename;
        return self::write($filename, json_encode($content, JSON_UNESCAPED_UNICODE), $overwrite);
    }
    
    public static function append($filename, $content){
        $filename = RUNTIME_DIR_DATA.$filename;
        file_put_contents($filename, $content, FILE_APPEND);
    }

    public static function check_dir($dirname, $flag = 0777){
        $dir = RUNTIME_DIR_DATA.$dirname;
        if(file_exists($dir)){
            return $dirname;
        }
        mkdir($dir, $flag, true);
        return $dirname;
    }

    public static function exists($filename){
        return file_exists(RUNTIME_DIR_DATA.$filename);
    }

    public static function is_dir($filename){
        return is_dir(RUNTIME_DIR_DATA.$filename);
    }

    public static function filesize($filename){
        return filesize(RUNTIME_DIR_DATA.$filename);
    }
}