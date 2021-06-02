<?php
namespace eco\cli;

class Console
{
    public static function log($text,$no=60,$model=1)
    {
        switch ($model) {
            case 1:
                echo "\033[48;5;".$no.";4;23m  ".$text."  \033[0m","\n";
                break;
            case 2:
                echo "\033[48;5;".$no.";23m  ".$text."  \033[0m","\n";
                break;
        }
    }
}
