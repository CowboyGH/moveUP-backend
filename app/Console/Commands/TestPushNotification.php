<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Illuminate\Console\Command;

class TestPushNotification extends Command
{
    protected $signature = 'push:test {user? : ID пользователя} {--all : Отправить всем пользователям}';
    protected $description = 'Тестовая отправка push уведомлений';

    public function handle()
    {
        $userId = $this->argument('user');
        $sendToAll = $this->option('all');

        if ($sendToAll) {
            $users = User::whereNotNull('fcm_token')->get();
            $count = $users->count();

            if ($count === 0) {
                $this->error('Нет пользователей с FCM токенами');
                return 1;
            }

            $this->info("Отправка тестового уведомления {$count} пользователям...");

            foreach ($users as $user) {
                $user->notify(new PushNotification(
                    '🔔 Тестовое уведомление',
                    'Это тестовое push-уведомление от сервера',
                    ['type' => 'test', 'timestamp' => now()->toDateTimeString()]
                ));
            }

            $this->info('Уведомления поставлены в очередь!');

        } elseif ($userId) {
            $user = User::find($userId);

            if (!$user) {
                $this->error("Пользователь с ID {$userId} не найден");
                return 1;
            }

            if (!$user->fcm_token) {
                $this->warn("У пользователя нет FCM токена");
                return 1;
            }

            $this->info("Отправка тестового уведомления пользователю {$user->name}...");

            $user->notify(new PushNotification(
                '👋 Привет!',
                'Это тестовое push-уведомление специально для тебя',
                ['type' => 'test', 'user_id' => $user->id]
            ));

            $this->info('Уведомление поставлено в очередь!');
        } else {
            $this->error('Укажите ID пользователя или опцию --all');
            return 1;
        }

        return 0;
    }
}
