<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiRequestException;
use App\Helpers\TelegramBotService;
use App\Models\Command;
use App\Models\User;

class BaseController
{
    protected TelegramBotService $telegram;
    protected User $user;

    public function init(TelegramBotService $telegram)
    {
        $this->telegram = $telegram;

        $this->user = $this->findOrCreateUser();

        return $this;
    }

    public function showLoading(): mixed
    {
        return $this->telegram->editOrSendNewMessage(" در حال دریافت اطلاعات ... لطفا صبر کنید ");
    }

    public function storeCommand(?string $command = null): ?Command
    {
        return (new Command())->insert($this->telegram->chatId(), $command ?? $this->telegram->text());
    }

    public function closeCommand(Command $command): static
    {
        $command->close();

        return $this;
    }

    private function findOrCreateUser(): ?User
    {
        $user = (new User)->where('chat_id', $this->telegram->chatId());

        if (! $user) {
            $user = (new User)->insert($this->telegram->chatId());
        }
        $this->user = $user;

        return $this->user;
    }

    /**
     * @throws ApiRequestException
     */
    public function request($action, $param)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://one-api.ir/instagram/?token=". env('ONE_API_TOKEN') ."&action={$action}&username={$param}",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $result =  json_decode($response);

        if ($result->status !== 200) {
            throw new ApiRequestException("متاسفانه مشکلی در اجرای درخواست شما بوجود آمده است");
        }

        return $result->result;
    }
}