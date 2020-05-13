<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\components;

class Json2Table
{

    public static function formatContent($content, $class = 'table table-bordered')
    {
        $html = "";
        if ($content != null) {
            $arr = json_decode(strip_tags($content), true);
            
            if ($arr && is_array($arr)) {
                $html .= self::arrayToHtmlTableRecursive($arr, $class);
            }
        }
        return $html;
    }

    public static function arrayToHtmlTableRecursive($arr, $class = 'table table-bordered')
    {
        $str = "<table class='$class'><tbody>";
        foreach ($arr as $key => $val) {
            $str .= "<tr>";
            $str .= "<td>$key</td>";
            $str .= "<td>";
            if (is_array($val)) {
                if (! empty($val)) {
                    $str .= self::arrayToHtmlTableRecursive($val, $class);
                }
            } else {
                $str .= "<strong>$val</strong>";
            }
            $str .= "</td></tr>";
        }
        $str .= "</tbody></table>";
        
        return $str;
    }
}