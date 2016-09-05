<?php 
function test() {
    //set_exception_handler('test2');
}

function test2(\Exception $exception) {
    var_dump($exception);
}