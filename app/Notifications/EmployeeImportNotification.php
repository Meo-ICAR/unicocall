<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeImportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public array $results;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $success = $this->results['imported'] > 0;
        $total = $this->results['imported'] + $this->results['skipped'];

        return (new MailMessage)
            ->subject($success ? '✅ Importazione Dipendenti Completata' : '⚠️ Importazione Dipendenti con Problemi')
            ->greeting('Ciao ' . $notifiable->name . ', '!')
            ->line("Riepilogo importazione dipendenti:")
            ->line("• Dipendenti importati: {$this->results['imported']}")
            ->line("• Dipendenti saltati: {$this->results['skipped']}")
            ->line("• Totale processati: {$total}")
            ->line("• Data: " . now()->format('d/m/Y H:i'))
            ->line("• File: dipendenti No&mi Gennaio 2026 (1).xlsx")
            ->line('')
            ->line($success ? "L'importazione è stata completata con successo!" : "L'importazione ha riscontrato alcuni problemi.")
            ->line($success ? 'Puoi ora visualizzare i dipendenti importati nel sistema.' : 'Controlla i log per maggiori dettagli.')
            ->action('Visualizza Dipendenti', url('/admin/employees'))
            ->line('Grazie per aver utilizzato il sistema di importazione!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'imported' => $this->results['imported'],
            'skipped' => $this->results['skipped'],
            'total' => $this->results['imported'] + $this->results['skipped'],
            'errors' => $this->results['errors'],
            'has_errors' => !empty($this->results['errors']),
            'success' => $this->results['imported'] > 0,
        ];
    }
}
