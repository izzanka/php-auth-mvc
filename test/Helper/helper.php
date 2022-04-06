<?php

namespace MVC\PHP\App {

    function header(string $value){
        echo $value;
    }

}

namespace MVC\PHP\Service {

    function setcookie(string $name, string $value){
        echo "$name: $value";
    }

}