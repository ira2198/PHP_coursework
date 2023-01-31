<?php

class MyErrorException extends Exception {}
class HttpErrorException extends Exception {}

function errFoo()
{
    try {
        throw new HttpErrorException ("error ");
        } catch (MyErrorException $exception) {
            echo "error 1 \n";
            echo $exception->getMessage();
            return false;
        }
    return true;
}


try {
echo "\n";
var_dump(errFoo());
echo "end \n";
} catch (Exception $exception){
    echo "error 2 \n";
    echo $exception->getMessage();
}
