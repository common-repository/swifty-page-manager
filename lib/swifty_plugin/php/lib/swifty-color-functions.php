<?php

if( ! class_exists( 'SwiftyColorFunctions' ) ) {

    class SwiftyColorFunctions
    {

        /**
         * Increase or decrease the given color with a percentage
         *
         * @param $supplied_hex color in hex format
         * @param $shift_method increase hex color with 'up', '+', 'lighter', '>', decrease with 'down', '-', 'darker', '<'
         * @param int $percentage increase or decrease with this percentage (default is 50)
         * @return string new hex color
         *
         */
        public static function hex_shift( $supplied_hex, $shift_method, $percentage = 50 )
        {
            $shifted_hex_value = null;
            $valid_shift_option = false;
            $current_set = 1;
            $RGB_values = array();
            $valid_shift_up_args = array( 'up', '+', 'lighter', '>' );
            $valid_shift_down_args = array( 'down', '-', 'darker', '<' );
            $shift_method = strtolower( trim( $shift_method ) );

            // Check Factor
            if( ! is_numeric( $percentage ) || ( $percentage = ( int ) $percentage ) < 0 || $percentage > 100
            ) {
                trigger_error( 'Invalid factor', E_USER_NOTICE );
            }

            // Check shift method
            foreach( array( $valid_shift_down_args, $valid_shift_up_args ) as $options ) {
                foreach( $options as $method ) {
                    if( $method == $shift_method ) {
                        $valid_shift_option = ! $valid_shift_option;
                        $shift_method = ( $current_set === 1 ) ? '+' : '-';
                        break 2;
                    }
                }

                ++$current_set;
            }

            if( ! $valid_shift_option ) {
                trigger_error( 'Invalid shift method', E_USER_NOTICE );
            }

            // Check Hex string
            switch( strlen( $supplied_hex = ( str_replace( '#', '', trim( $supplied_hex ) ) ) ) ) {
                case 3:
                    if( preg_match( '/^([0-9a-f])([0-9a-f])([0-9a-f])/i', $supplied_hex ) ) {
                        $supplied_hex = preg_replace( '/^([0-9a-f])([0-9a-f])([0-9a-f])/i', '\\1\\1\\2\\2\\3\\3',
                            $supplied_hex
                        );
                    } else {
                        trigger_error( 'Invalid hex color value', E_USER_NOTICE );
                    }
                    break;
                case 6:
                    if( ! preg_match( '/^[0-9a-f]{2}[0-9a-f]{2}[0-9a-f]{2}$/i', $supplied_hex ) ) {
                        trigger_error( 'Invalid hex color value', E_USER_NOTICE );
                    }
                    break;
                default:
                    trigger_error( 'Invalid hex color length', E_USER_NOTICE );
            }

            // Start shifting
            $RGB_values[ 'R' ] = hexdec( $supplied_hex{0} . $supplied_hex{1} );
            $RGB_values[ 'G' ] = hexdec( $supplied_hex{2} . $supplied_hex{3} );
            $RGB_values[ 'B' ] = hexdec( $supplied_hex{4} . $supplied_hex{5} );

            foreach( $RGB_values as $c => $v ) {
                switch( $shift_method ) {
                    case '-':
                        $amount = round( ( ( 255 - $v ) / 100 ) * $percentage ) + $v;
                        break;
                    case '+':
                        $amount = $v - round( ( $v / 100 ) * $percentage );
                        break;
                    default:
                        trigger_error( 'Oops. Unexpected shift method', E_USER_NOTICE );
                }

                $shifted_hex_value .= $current_value = ( strlen( $decimal_to_hex = dechex( $amount ) ) < 2 ) ?
                    '0' . $decimal_to_hex : $decimal_to_hex;
            }

            return '#' . $shifted_hex_value;
        }
    }
}