<?php
/*
*-------------------------------------RSALib-----------------------------------*
*----    An implementation of the RSA (Rivest/Shamir/Adelman) algorithm    ----*
*----------                http://www.rsasecurity.com                ----------*
*------------------------------------------------------------------------------*
*------------------------------------------------------------------------------*
*----     This library is free software; you can redistribute it and/or    ----*
*----      modify it under the terms of the GNU Lesser General Public      ----*
*----     License as published by the Free Software Foundation; either     ----*
*----  version 2.1 of the License, or (at your option) any later version.  ----*
*----                                                                      ----*
*----   This library is distributed in the hope that it will be useful,    ----*
*----    but WITHOUT ANY WARRANTY; without even the implied warranty of    ----*
*----  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU   ----*
*----            Lesser General Public License for more details.           ----*
*----                                                                      ----*
*----   You should have received a copy of the GNU Lesser General Public   ----*
*----  License along with this library; if not, write to the Free Software ----*
*----        Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,        ----*
*----                          MA  02110-1301  USA                         ----*
*------------------------------------------------------------------------------*
*------------------------------------------------------------------------------*
*------                Copyright © 2005 Antonio Anzivino                 ------*
*------------------------------------------------------------------------------*
*/

class RSA {
        var $n; //modulo
        var $e; //public
        var $d; //private

        /*
        CONSTRUCTOR
        Initializes the RSA Engine with given RSA Key Pair. You must have run
        keygen.php and obtained a valid RSA Key Pair
        */
        function RSA($n = 0, $e = 0, $d = 0) {
                
                $this->n = $n;
                $this->e = $e;
                $this->d = $d;

               
               return true;
        }

        /*
        CONVERSIONS STRING-BINARY
        */
        function bin2asc ($temp) {
                $data = "";
                for ($i=0; $i<strlen($temp)-1 ; $i+=8) $data .= chr(bindec(substr($temp,$i,8)));
                return $data;
        }

        function asc2bin ($temp) {
                $data = "";
                for ($i=0; $i<$strlen($temp)-1; $i++) $data .= sprintf("%08b",ord($temp[$i]));
                return $data;
        }


        /*
        MODULUS FUNCTION
        */
        function mo ($g, $l) {
                return $g - ($l * floor ($g/$l));
        }
        /*
        RUSSIAN PAESANT method for exponentiation
        */
        function powmod ($base, $exp, $modulus) {
                $accum = 1;
                $i = 0;
                $basepow2 = $base;
                while (($exp >> $i)>0) {
                        if ((($exp >> $i) & 1) == 1) {
                                $accum = $this->mo(($accum * $basepow2) , $modulus);
                        }
                        $basepow2 = $this->mo(($basepow2 * $basepow2) , $modulus);
                        $i++;
                }
                return $accum;
        }

 
        public function encrypt ($m, $s=3) {
        $coded   = '';
        $max     = strlen($m);
        $packets = ceil($max/$s);
        
        for($i=0; $i<$packets; $i++){
            $packet = substr($m, $i*$s, $s);
            $code   = '0';

            for($j=0; $j<$s; $j++){
                $code = bcadd($code, bcmul(ord($packet[$j]), bcpow('256',$j)));
            }

            $code   = bcpowmod($code, $this->e, $this->n);
            $coded .= $code.' ';
        }

      	return trim($coded);
    }
        
      public function decrypt ($c) {
        $coded   = explode(' ', $c);
        $message = '';
        $max     = count($coded);
        for($i=0; $i<$max; $i++){
            $code = bcmod(bcpow($coded[$i], $this->d), $this->n);
            
            while(bccomp($code, '0') != 0){
                $ascii    = bcmod($code, '256');
                $code     = bcdiv($code, '256', 0);
                $message .= chr($ascii);
            }
        }

        return $message;
    }
}
?>