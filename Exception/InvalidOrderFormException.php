<?php
namespace CartBundle\Exception;
use LazyRecord\Result;
use Exception;

class InvalidOrderFormException extends CheckoutException
{
    protected $result;

    public function __construct($message, Result $result, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->result = $result;
    }

    public function __debugInfo()
    {
        return [
            'result' => $this->result,
        ];
    }
}
