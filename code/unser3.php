<?php
        var $test = '123';
        function __wakeup(){
            $fp = fopen("shell.php","w") ;
            fwrite($fp,$this->test);
            fclose($fp);
        }
    }
    print_r($class4_ser);