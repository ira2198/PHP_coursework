<?php
namespace GeekBrains\LevelTwo\Http;

class ErrResponse extends Response 
{
    protected const SUCCESS = false; // переопределяем, не успешный ответ

    public function __construct(
        private string $reason = 'Something goes wrong'
    )
    {        
    }

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}