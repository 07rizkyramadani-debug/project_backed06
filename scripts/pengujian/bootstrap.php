<?php
// Bootstrap aplikasi Laravel untuk skrip pengujian (dijalankan dari root project).
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
function qcount(Closure $fn): int { DB::flushQueryLog(); DB::enableQueryLog(); $fn(); $n = count(DB::getQueryLog()); DB::disableQueryLog(); return $n; }
