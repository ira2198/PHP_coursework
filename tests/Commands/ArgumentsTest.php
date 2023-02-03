<?php

namespace Commands;


use GeekBrains\LevelTwo\Users\Commands\Arguments;
use GeekBrains\LevelTwo\Users\Exceptions\ArgumentsException;

use PHPUnit\Framework\TestCase;


class ArgumentsTest extends TestCase  // проверка не строгая
{
    public function testItReturnsValuesAsStrings(): void
    {
    // подготовка , сщздали экземпляр и передали значения
        $arguments = new Arguments(['some_key' => 'some_value']);
    //  действие , получили элемент
        $value = $arguments->get('some_key');

    // проверка , сравнили правильно ли он это он
        $this->assertEquals('some_value', $value);  // ssertEquals сравнивает значения, assertSame сравнивает значения и типы
        $this->assertIsString($value); //строка ли 

    }


// Тест на исключения  

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        // Подготавливаем объект с пустым набором данных
        $arguments = new Arguments([]);
        
        // Описываем тип ожидаемого исключения
        $this->expectException(ArgumentsException::class);
        // и его сообщение        
        $this->expectExceptionMessage("No such argument: some_key");
        
        // Выполняем действие, приводящее к выбрасыванию исключения
        $arguments->get('some_key');
    }

  // Провайдер данных

    public function argumentsPrivider (): iterable  //iterable почти как arr, чтобы можно было перебирать в цикле
    {
        return[

            ['some_string', 'some_string'], // Тестовый набоp
            // Первое значение будет передано  в тест первым аргументом,
            // второе значение будет передано в тест вторым аргументом
            [' some_string', 'some_string'], // Тестовый набор №2
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
        ];
    }

/**
 * @dataProvider argumentsPrivider
 * @throws ArgumentsException
 * необходимо для связи теста с провайдером
 */
    public function testItConvertsArgumentsToStrings($inputValue, $expectedValue): void
    {
        // Подставляем первое значение из тестового набора
        $arguments = new Arguments(['some_key' => $inputValue]);

        $value = $arguments->get('some_key');

        // Сверяем со вторым значением из тестового набора
        $this->assertEquals($expectedValue, $value);
    }
    
        

}