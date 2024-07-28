<?php

namespace App\Core;

use App\Helpers\TelegramBotService;
use App\Models\Command;

class BotRouter
{
    public function __construct(protected TelegramBotService $telegram)
    {
    }

    protected array $routes = [];

    protected function add(string $type, string $command, $controller, $method = null, $name = null): static
    {
        $this->routes[] = [
            'type' => $type,
            'command' => $command,
            'controller' => $controller,
            'method' => $method,
            'middleware' => null,
            'name' => $name
        ];

        return $this;
    }

    public function exact($text, $controller, $method = null, $name = null): static
    {
        return $this->add('exact', $text, $controller, $method, $name);
    }

    public function startsWith($text, $controller, $method = null, $name = null): static
    {
        return $this->add('starts_with', $text, $controller, $method, $name);
    }

    public function after($text, $controller, $method = null, $name = null): static
    {
        return $this->add('after', $text, $controller, $method, $name);
    }

    public function default($controller, $method = null, $name = null)
    {
        return $this->add('default', 'default', $controller, $method, $name);
    }

    public function match()
    {
        $user_command = $this->telegram->text();
        $latestStoredCommand = (new Command)->byChatId($this->telegram->chatId());

        foreach ($this->routes as $route) {
            $type = $route['type'];
            $controller = $route['controller'];
            $method = $route['method'] ?? 'handle';

            if ($type === 'exact' && $route['command'] === $user_command) {
                return (new $controller)->init($this->telegram)->$method($user_command);
            }

            if ($type === 'starts_with' && str_starts_with($user_command, $route['command'])) {
                $param = str_replace($route['command'], '', $user_command);

                return (new $controller)->init($this->telegram)->$method($user_command, $param);
            }

            if ($type === 'after' && $latestStoredCommand?->command === $route['command']) {
                return (new $controller)->init($this->telegram)->closeCommand($latestStoredCommand)->$method($user_command);
            }

            if ($type === 'default') {
                return (new $controller)->init($this->telegram)->$method($user_command);
            }
        }
    }
}