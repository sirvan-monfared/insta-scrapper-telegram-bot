<?php
use App\Core\BotRouter;
use App\Helpers\TelegramBotService;

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//ini_set("display_errors", 0);

const BASE_PATH = __DIR__ . '/../';
const SITE_URL = '';


require BASE_PATH . "vendor/autoload.php";

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

$telegram = new TelegramBotService();
$router = new BotRouter($telegram);

require(base_path('routes/routes.php'));

$router->match();
