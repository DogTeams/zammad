<?php
/**
 * @package Zammad API Wrapper
 * @author  Jordan GOBLET <jordan.goblet.pro@gmail.com>
 */
namespace Dogteam\Zammad\Exception;

class TypeException extends ZammadException{
    public function __construct()
    {
        $message = $this->create(func_get_args());
        parent::__construct($message);
    }
}
