<?php

namespace App;

use PHPUnit\Framework\Attributes\DataProvider;

final readonly class Rando
{
    #[DataProvider("thing")]
    public function NotARouteAttr()
    {

    }
}