<?php

use App\Models\Conversation;
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

        $userId = Auth::id();

        if ($userId !== $this->listing->creator_id) {
            $conversation = $this->listing->conversationWith(Auth::user());

            if ($conversation) {
                $this->authorize('view', $conversation);
            }
        }
    }

    public function send()
    {
        $this->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $userId = Auth::id();

        $conversation = $this->listing->conversations()
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('listing_creator_id', $userId);
            })
            ->first();

        if (! $conversation) {
            $isCreator = $userId === $this->listing->creator_id;
            $inquirerId = $isCreator ? null : $userId;

            $conversation = $this->listing->conversations()->create([
                'user_id' => $inquirerId,
                'listing_creator_id' => $this->listing->creator_id,
            ]);
        }

        $message = $conversation->messages()->create([
            'user_id' => $userId,
            'team_id' => $conversation->team_id,
            'content' => $this->content,
        ]);

        $recipient = $conversation->otherUser(Auth::user());

        if ($recipient) {
            Notification::send($recipient, new NewMessageNotification($message));
        }

        $this->content = '';
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function conversation(): ?Conversation
    {
        return $this->listing->conversationWith($this->user);
    }

    #[Computed]
    public function team()
    {
        return $this->user->currentTeam;
    }
}
?>

<section class="mx-auto max-w-4xl space-y-8">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('listings.show', $listing) }}" wire:navigate variant="ghost">
            Back
        </flux:button>
        <flux:heading size="xl">Conversation about {{ $listing->title }}</flux:heading>
    </div>

    @if ($this->conversation)
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-4">
                <flux:heading size="lg">Listing details</flux:heading>
                <flux:text>{{ $listing->description }}</flux:text>
            </div>

            <div class="space-y-4">
                @foreach ($this->conversation->messages as $message)
                    <div class="{{ $message->user_id === $this->user->id ? 'ml-auto' : 'mr-auto' }} max-w-2xl">
                        <div class="{{ $message->user_id === $this->user->id ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }} rounded-lg border p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <flux:text size="sm" variant="muted">
                                    {{ $message->user->name }}
                                </flux:text>
                                <flux:text size="sm" variant="muted">
                                    {{ $message->created_at->format('M j, Y g:i A') }}
                                </flux:text>
                            </div>
                            <flux:text>{{ $message->content }}</flux:text>
                        </div>
                    </div>
                @endforeach
            </div>

            <form wire:submit="send" class="w-full space-y-4">
                <flux:textarea wire:model="content" label="Your message" rows="4" required />
                <flux:button variant="primary" type="submit">Send</flux:button>
            </form>
        </div>
    @else
        <div class="text-center py-12">
            <flux:text>No conversation found.</flux:text>
        </div>
    @endif
</section>
