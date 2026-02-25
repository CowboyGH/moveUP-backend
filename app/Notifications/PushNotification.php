<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use DevKandil\NotiFire\FcmMessage;
use DevKandil\NotiFire\Enums\MessagePriority;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Создать новый экземпляр уведомления
     */
    public function __construct(
        protected string $title,
        protected string $body,
        protected array $data = [],
        protected ?string $imageUrl = null
    ) {
        $this->onQueue('push-notifications');
    }

    /**
     * Получить каналы доставки уведомления
     */
    public function via($notifiable): array
    {
        return $notifiable->fcm_token ? ['fcm'] : [];
    }

    /**
     * Получить FCM представление уведомления
     */
    public function toFcm($notifiable): FcmMessage
    {
        $message = FcmMessage::create($this->title, $this->body)
            ->sound('default')
            ->priority(MessagePriority::HIGH)
            ->data(array_merge($this->data, [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'user_id' => (string) $notifiable->id,
            ]));

        if ($this->imageUrl) {
            $message->image($this->imageUrl);
        }

        return $message;
    }

    /**
     * Получить массив данных уведомления
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
