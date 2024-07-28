<?php

namespace App\Http\Controllers;

class InfoController extends BaseController
{
    public function get(): void
    {
        $this->storeCommand('/info');
        $this->telegram->sendMessage("نام کاربری صفحه مورد نظر رو وارد کن");
    }

    public function send($command): void
    {
        $loading = $this->showLoading();

        try {
            $result = $this->request('user', $command);

            $output = "آیدی عددی: {$result->id} \n\n";
            $output .= "تعداد فالوئر: {$result->followers} \n\n";
            $output .= "تعداد فالوئینگ: {$result->following} \n\n";

            $imageData = downloadRemoteImage($result->profile_hd);

            $this->telegram->sendPhoto($imageData, caption: $output);
            $this->telegram->deleteMessage($loading);
        } catch (\Exception $e) {
            $this->telegram->sendMessage($e->getMessage());
        }
    }
}