<?php

namespace GeekBrains\LevelTwo\Container;

interface ContainerInterface 
{
    /**
    * Находит объект по его идентификатору и возвращает его.
    *
    * @param string $id Идентификатор искомого объекта.    *
    * @throws NotFoundExceptionInterface Объект не найден.
    * @throws ContainerExceptionInterface Ошибка получения объекта
    *
    * @return mixed Объект.
    */


    public function get(string $type);
    /**
    * Возвращает true, если контейнер может вернуть объект
    * по этому идентификатору, false – в противном случае
    * Если `has($id)` возвращает true, это не значит,
    * что `get($id)` не выбросит исключения.
    * Это значит, однако, что `get($id)`
    * не выбросит исключения `NotFoundExceptionInterface`.
    *
    * @param string $id Идентификатор искомого объекта.
    *
    * @return bool
    */

    public function has(string $type);

}