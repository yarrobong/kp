<?php

require_once __DIR__ . '/../bootstrap/app.php';

use App\Support\Session;
use routes\Route;

Session::start();

// Загружаем маршруты
require_once __DIR__ . '/../routes/web.php';

// Диспетчеризация
Route::dispatch();


