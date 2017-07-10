<?php
class Zend_View_Helper_TimeToString extends Zend_View_Helper_Abstract
{

    public function TimeToString($timestamp, $format)
    {
        $prefix = '';
        $delta = time() - $timestamp;
        if ($delta < 0) $prefix = 'in ';
        else $prefix = 'delay ';
        if (abs($delta) < 3600) return $prefix . round(abs($delta)/60) . 'min.';
        else return date($format, $timestamp);
    }
}