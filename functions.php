<?php

use App\Core\Authenticator;
use App\Core\Session;

function currentUrl()
{
    $url = parse_url($_SERVER['REQUEST_URI']);

    return $url['path'];
}

function isUrl($url): bool
{
    return currentUrl() === $url;
}

function e($value): string
{
    return htmlspecialchars($value);
}


function base_path($path): string
{
    return BASE_PATH . $path;
}

function old($key, $default = '')
{
    return Session::get('old')[$key] ?? $default;
}


function asset($path): string
{
    return SITE_URL . "assets/$path";
}

function priceFormat($price): string
{
    return number_format($price) . " تومان";
}

function str_limit($str, $limit): string
{
    if (strlen($str) > $limit) {
        return mb_substr($str, 0, $limit) . ' ...';
    }

    return $str;
}

function now($format = 'Y-m-d H:i:s'): string
{
    return date($format);
}

function auth(): Authenticator
{
    return new Authenticator;
}

function url($route_name, $params = []): string
{
    return rtrim(SITE_URL, '/') . route($route_name, $params);
}

function generateRandom($length = 20): string
{
    return bin2hex(random_bytes($length));
}

function env(string $key, $default = ''): string
{
    return $_ENV[$key] ?? $default;
}

function httpRequestMethod(): ?string
{
    return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
}

function shamsi($date, $format = 'Y/m/d'): string
{
    return jdate($date)->format($format);
}

function downloadRemoteImage($image_url): bool|string
{
    $ch = curl_init($image_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    if (env('USE_PROXY')) {
        curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:12334');
    }

    $imageData = curl_exec($ch);
    curl_close($ch);

    return $imageData;
}