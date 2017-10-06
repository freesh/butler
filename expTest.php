<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

$language = new ExpressionLanguage();

var_dump($language->evaluate('1 + 2')); // displays 3

var_dump($language->compile('1 + 2')); // displays (1 + 2)



class Apple
{
    public $variety;
}

$runtime = array (
    'first' => '1',
    'last' => '6'
);

$rt = (object) $runtime;

var_dump($rt);

$apple = new Apple();
$apple->variety = '5';

var_dump($apple);

var_dump($language->evaluate(
    'fruit.variety + rt.first + rt.last',
    array(
        'fruit' => $apple,
        'rt' => $rt
    )
));