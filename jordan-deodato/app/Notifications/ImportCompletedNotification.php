<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ImportCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $fileName,
        public int $importedCount,
        public int $errors
    ) {
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('✅ Importação de CSV concluída')
            ->line("A importação do arquivo {$this->fileName} foi concluída.")
            ->line("Registros importados: {$this->importedCount}")
            ->line("Erros encontrados: {$this->errors}")
            ->action('Ver dados', url('/imported-data'));
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Importação de {$this->fileName} concluída",
            'imported' => $this->importedCount,
            'errors' => $this->errors,
            'action_url' => '/imported-data'
        ];
    }
}