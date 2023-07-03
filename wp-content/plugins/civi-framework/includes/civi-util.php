<?php

if ( !function_exists('issetval') ) {
    /**
     *  Perform isset() check and return value if true
     * 
     *  @return string|array|boolean Value of the variable or false if null
     */
    function isset_val( &$variable )
    {
        return (isset($variable)) ? $variable : false;
    } 
}

if ( !function_exists('isset_val_unseri') ) {
    /**
     *  Perform isset() on an array key, check, unserilize and return value of key if true
     * 
     *  @param array    &$array 
     *  @param string   $key
     * 
     *  @return string|array|boolean Value or false if null
     */
    function isset_val_unseri( &$array, $key )
    {
        if ( is_array($array) ) {
            $value = (isset($array[$key])) ? $array[$key] : false;
            if ($value != false) {
                $value = unserialize( $value[0] );
            }   
            return $value ;
        }

        return $array;
    } 
}