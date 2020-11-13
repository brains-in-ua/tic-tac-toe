<?php

interface DataStorage
{
    public function write($key, $value);
    public function read($key);
    public function delete($key);

}
