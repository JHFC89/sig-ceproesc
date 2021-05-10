<?php

if (! function_exists('show_route')) {
    function sig_format_decimal_number($number, $precision = 2, $separator = '.')
    {
        $numberParts = explode($separator, $number);

        $response = $numberParts[0];

        if (count($numberParts)>1 && $precision > 0) {
            $response .= $separator;
            $response .= substr($numberParts[1], 0, $precision);
        }

        return $response;
    }
}
