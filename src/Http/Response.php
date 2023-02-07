<?php

namespace GeekBrains\LevelTwo\Http;

abstract class Response 
{
    protected const SUCCESS = true; //Mаркировка успешности ответа

    public function send():void
    {
        // данные ответа 
        // + объединяет 2 массива в 1
        $data = ['success'=> static::SUCCESS] + $this->payload();

        // заголовок, говорящий что в  теле ответа JSON
        header('Content-Type: application/json');   
        
        //кодируем JSON и возвращаем их в теле ответа
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
    // абстрактный метод, возвращающий полезны е данные ответа
    abstract protected function payload(): array;

}