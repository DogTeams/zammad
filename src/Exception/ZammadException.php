<?php
/**
 * @package Zammad API Wrapper
 * @author  Jordan GOBLET <jordan.goblet.pro@gmail.com>
 */
namespace Dogteam\Zammad\Exception;

use Exception;

abstract class ZammadException extends Exception{
    protected $id;
    protected $details;

    public function __construct($message)
    {
        parent::__construct($message);
    }

    protected function create(array $args)
    {
        $this->id = array_shift($args);
        $error = $this->errors($this->id);
        $this->details = vsprintf($error['context'], $args);
        return $this->details;
    }

    private function errors($id)
    {
        $data= [
            'not_found' => [
                'context'  => 'The requested resource could not be found.',
            ],
            'missing_parameter' => [
                'context' => 'Missing parameters.'
            ]
        ];
        return $data[$id];
    }
}
