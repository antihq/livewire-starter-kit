<?php

use App\Models\Listing;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Listing $listing;

    public string $content = '';

    public function mount()
    {
        $this->authorize('view', $this->listing);

        if (Auth::id() === $this->listing->creator_id) {
            return $this->redirectRoute('listings.conversation', $this->listing);
        }
    }

    public function send()
    {
        $this->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = $this->listing->conversations()->firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'team_id' => $this->listing->team_id,
                'listing_creator_id' => $this->listing->creator_id,
            ]
        );

        $message = $conversation->messages()->create([
            'user_id' => Auth::id(),
            'team_id' => $conversation->team_id,
            'content' => $this->content,
        ]);

        Notification::send($this->listing->creator, new NewMessageNotification($message));

        session()->flash('status', 'Message sent successfully!');

        return $this->redirectRoute('listings.show', $this->listing);
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function team()
    {
        return $this->user->currentTeam;
    }
}
?>

<section class="mx-auto max-w-lg">
    <flux:heading size="xl">Send a message about {{ $listing->title }}</flux:heading>

    <div class="mt-12">
        <form wire:submit="send" class="w-full space-y-8">
            <flux:textarea wire:model="content" label="Your message" rows="6" required autofocus />

            <div class="flex justify-end gap-4">
                <flux:button href="{{ route('listings.show', $listing) }}" wire:navigate variant="ghost">
                    Cancel
                </flux:button>
                <flux:button variant="primary" type="submit">Send message</flux:button>
            </div>
        </form>
    </div>
</section>
