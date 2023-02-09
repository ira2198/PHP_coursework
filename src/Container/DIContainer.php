<?php

namespace GeekBrains\LevelTwo\Container;

use GeekBrains\LevelTwo\Users\Exceptions\ContainerNotFoundException;
use ReflectionClass;

class DIContainer implements ContainerInterface
{ 
    // Массив правил создания объектов
    private array $resolvers = [];

    // Метод для добавления правил
    public function bind(string $type, $resolvers)
    {
        $this->resolvers[$type] = $resolvers;
    }

    public function get(string $type): object
    {
        // Проверяем есть ли правило для создания обЪекта
        if(array_key_exists($type, $this->resolvers)){

            // создаем объект класса по указанному правилу
            $typeToCreate = $this->resolvers[$type]; // т.е. Вызываем тот же самый метод контейнера
            // и передаём в него имя класса, указанного в правиле

            // Если в контейнере для запрашиваемого типа
            // уже есть готовый объект — возвращаем его
            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }

            return $this->get($typeToCreate);
        }    

        // иначе создаем просто на прямую
        if(!class_exists($type)){
            throw new ContainerNotFoundException("Cannot resolve type: $type");
        }

        // класс для препарирования других классов
        // Создаём объект рефлексии для запрашиваемого класса
        $reflectionClass = new ReflectionClass($type); //глобальный класс
        
        //Исследуем конструктор
        $constructor = $reflectionClass->getConstructor();

        // если конструктора нет:
        if(null === $constructor) {
            return new $type();
        }

        // иначе, сюда собираем обЪекты зависимостей
        $parametrs = [];

        foreach($constructor->getParameters() as $parametr) {
            $parametrType = $parametr->getType()->getName(); //узнаем тип параметра

            $parametrs[] = $this->get($parametrType); // получаем объект зависимости
        }

        return new $type(...$parametrs);
    }


    public function has(string $type)
    {
        // Здесь мы пытаемся создать объект требуемого типа
        try {
            $this->get($type);
            } catch (ContainerNotFoundException $err) {
            // Возвращаем false, если объект не создан...
            return false;
            }
            // и true, если создан
        return true;
    }
   


}