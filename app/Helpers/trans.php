<?php
// 转换相关的函数

/**
 * 数字转字符串
 *
 * @param int $val 数字
 * @return string 字符串
 */
if (!function_exists('num2str')) {
    function num2str($val, $decimals = 2)
    {
        return rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.');
    }
}

/**
 * 文件大小转换
 *
 * @param int $filesize 文件大小字节数
 * @return string 文件大小结果字符串
 */
if (!function_exists('transSize')) {
    function transSize($filesize)
    {
        if ($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
        } elseif ($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
        } elseif ($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
        } else {
            $filesize = $filesize . ' 字节';
        }

        return $filesize;
    }
}

/**
 * 文件大小转换为字节数
 *
 * @param int       $size   文件大小字节数
 * @param string    $unit   单位
 * @return int              文件大小
 */
if (!function_exists('transByte')) {
    function transByte($size, $unit)
    {
        if (!is_numeric($size)) {
            return 0;
        }
        if (strtolower($unit) == 'kb') {
            return $size * 1024;
        } elseif (strtolower($unit) == 'mb') {
            return $size * 1024 * 1024;
        } elseif (strtolower($unit) == 'gb') {
            return $size * 1024 * 1024 * 1024;
        }

        return 0;
    }
}

/**
 * 时间转换
 *
 * @param int $timestamp Unix时间戳
 * @param string 时间结果结果字符串
 */
if (!function_exists('transTimestamp')) {
    function transTimestamp($timestamp)
    {
        return $timestamp?date('Y-m-d H:i:s', $timestamp):'0000-00-00 00:00:00';
    }
}
