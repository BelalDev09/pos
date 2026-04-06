<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminContactNotification extends Notification
{
    use Queueable;

    /**
     * @var \App\Models\Contact
     */
    public $contact;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Contact $contact
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Contact Request Received')
                    ->greeting('Hello Admin!')
                    ->line('A new contact request has been submitted.')
                    ->line('**Name:** ' . $this->contact->first_name . ' ' . $this->contact->last_name)
                    ->line('**Email:** ' . $this->contact->email)
                    ->line('**Phone:** ' . $this->contact->phone)
                    ->line('**Message:** ' . ($this->contact->message ?? 'N/A'))
                    ->action('View Contact Requests', url('/admin/contact-requests'))
                    ->line('Thank you for your attention!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}