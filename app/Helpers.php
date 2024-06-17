<?php
    function cnsIsValid($cns) { 
        if (in_array($cns[0],[1,2])) {
            if ((strlen(trim($cns))) != 15) { 
                return false;
            }
            $pis = substr($cns,0,11);
            $soma = (((substr($pis, 0,1)) * 15) +
                     ((substr($pis, 1,1)) * 14) +
                     ((substr($pis, 2,1)) * 13) +
                     ((substr($pis, 3,1)) * 12) +
                     ((substr($pis, 4,1)) * 11) +
                     ((substr($pis, 5,1)) * 10) +
                     ((substr($pis, 6,1)) * 9) +
                     ((substr($pis, 7,1)) * 8) +
                     ((substr($pis, 8,1)) * 7) +
                     ((substr($pis, 9,1)) * 6) +
                     ((substr($pis, 10,1)) * 5));
            $resto = fmod($soma, 11);
            $dv = 11  - $resto;
            if ($dv == 11) { 
                $dv = 0;	
            }
            if ($dv == 10) { 
                $soma = ((((substr($pis, 0,1)) * 15) +
                          ((substr($pis, 1,1)) * 14) +
                          ((substr($pis, 2,1)) * 13) +
                          ((substr($pis, 3,1)) * 12) +
                          ((substr($pis, 4,1)) * 11) +
                          ((substr($pis, 5,1)) * 10) +
                          ((substr($pis, 6,1)) * 9) +
                          ((substr($pis, 7,1)) * 8) +
                          ((substr($pis, 8,1)) * 7) +
                          ((substr($pis, 9,1)) * 6) +
                          ((substr($pis, 10,1)) * 5)) + 2);
                $resto = fmod($soma, 11);
                $dv = 11  - $resto;
                $resultado = $pis."001".$dv;	
            } else { 
                $resultado = $pis."000".$dv;
            }
            if ($cns != $resultado){
                return false;
            } else {
                return true;
            }
        } else if (in_array($cns[0],[7,8,9])) {
            if ((strlen(trim($cns))) != 15) { 
                return false;
            }
            $soma = (((substr($cns,0,1)) * 15) +
            ((substr($cns,1,1)) * 14) +
            ((substr($cns,2,1)) * 13) +
            ((substr($cns,3,1)) * 12) +
            ((substr($cns,4,1)) * 11) +
            ((substr($cns,5,1)) * 10) +
            ((substr($cns,6,1)) * 9) +
            ((substr($cns,7,1)) * 8) +
            ((substr($cns,8,1)) * 7) +
            ((substr($cns,9,1)) * 6) +
            ((substr($cns,10,1)) * 5) +
            ((substr($cns,11,1)) * 4) +
            ((substr($cns,12,1)) * 3) +
            ((substr($cns,13,1)) * 2) +
            ((substr($cns,14,1)) * 1));	
            $resto = fmod($soma,11);
            if ($resto != 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
