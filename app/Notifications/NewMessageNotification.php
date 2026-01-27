<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $sender = $this->message->user->name ?? $this->message->user->email;
        $listing = $this->message->conversation->listing;
        $marketplace = $listing->marketplace;

        return (new MailMessage)
            ->subject("New message about your listing: {$listing->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$sender} sent you a message about your listing in {$marketplace->name}.")
            ->line("Listing: {$listing->title}")
            ->line("Message: {$this->message->content}")
            ->action('View Conversation', route('marketplaces.listings.conversation', [
                'marketplace' => $marketplace,
                'listing' => $listing,
            ]))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'listing_id' => $this->message->conversation->listing_id,
            'sender_id' => $this->message->user_id,
        ];
    }
}
