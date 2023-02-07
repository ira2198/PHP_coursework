<?php
declare(strict_types=1);


namespace GeekBrains\LevelTwo\Http;

use GeekBrains\LevelTwo\Http\Response;

class SuccessFullResponse extends Response 
{
    protected const SUCCESS =  true;

    public function __construct(
        private array $data = []
    )
    {        
    }
    protected function payload(): array
    {
        return['data' => $this->data];
    }
}