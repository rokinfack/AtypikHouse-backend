<?php

namespace App\Dto\Output;

use Symfony\Component\HttpFoundation\Response;

class StandardResponseOutput
{
    /**
     * @var string
     */
    public string $message;


    /**
     * @var int
     */
    public int $status = Response::HTTP_NO_CONTENT;

//    /**
//     * @param string $message
//     * @param int $status
//     */
//    public function __construct(string $message, int $status)
//    {
//        $this->message = $message;
//        $this->status = $status;
//    }
}