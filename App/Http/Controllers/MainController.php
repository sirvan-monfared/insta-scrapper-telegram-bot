<?php

namespace App\Http\Controllers;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MainController extends BaseController
{
    public function start(): void
    {
        $menu = [
            ['اطلاعات حساب' => '/info', 'استوری ها' => 'stories']
        ];

        $this->telegram->sendMessage("یکی از گزینه ها رو انتخاب کن", $menu);
    }
}