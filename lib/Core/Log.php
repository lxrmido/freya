<?php

namespace Lib\Core;

class Log{

    public static function timeline($action, $start, $end){
        $s = "[$action] from" . 
            date('Y-m-d H:i:s', intval($start)) . 
            ', ' . ($end - $start) . "s.\n";
        Data::check_dir('log');
        Data::append('log/tml_'.date("Y-m-d").".txt", $s);
    }

    public static function error($title = 'NoName', $content = ''){
        $s  = "\n";
        $s .= "[ErrorType] " . $title . "\n";
        $s .= "[Time] " . date("Y-m-d H:i:s") . "\n";
        if(is_string($content)){
            $s .= "[Content] $content";
        }else{
            $s .= "[Content] " . json_encode($content, JSON_UNESCAPED_UNICODE);
        }
        $s .= "\n";
        Data::check_dir('log');
        Data::append('log/err_'.date("Y-m-d").".txt", $s);
    }

    public static function danger($title = 'NoName', $content = ''){
    	$s  = "\n";
        $s .= "[ErrorType] " . $title . "\n";
        $s .= "[Time] " . date("Y-m-d H:i:s") . "\n";
        $s .= "[Content] " . json_encode($content, JSON_UNESCAPED_UNICODE);
        $s .= "\n";
        Data::check_dir('log');
        Data::append('log/dgr_'.date("Y-m-d").".txt", $s);
    }

    public static function debug($title = 'NoName', $content = ''){
        $s  = "\n----------\n";
        $s .= "[Type] " . $title . "\n";
        $s .= "[Time] " . date("Y-m-d H:i:s") . "\n";
        if(is_string($content)){
            $s .= "[Content]\n$content";
        }else{
            $s .= "[Content]\n" . json_encode($content, JSON_UNESCAPED_UNICODE);
        }
        $s .= "\n----------\n";
        Data::check_dir('log');
        Data::append('log/dbg_'.date("Y-m-d").".txt", $s);
    }

}