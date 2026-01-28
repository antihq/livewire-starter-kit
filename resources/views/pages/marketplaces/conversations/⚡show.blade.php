<?php

use App\Models\Conversation;
use App\Models\Marketplace;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public Conversation $conversation;

    public string $content = '';

    public function mount(Marketplace $marketplace, Conversation $conversation)
    {
        $this->authorize('view', $marketplace);

        if ($conversation->listing->marketplace_id !== $marketplace->id) {
            abort(404);
        }

        $this->conversation = $conversation->load(['messages.user', 'listing.user']);

        $this->authorize('view', $this->conversation);
    }

    public function send()
    {
        $this->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $message = $this->conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $this->content,
        ]);

        $recipient = $this->conversation->otherUser(Auth::user());

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
    public function team()
    {
        return $this->user->currentTeam;
    }

    #[Computed]
    public function otherUser()
    {
        return $this->conversation->otherUser($this->user);
    }
}
?>

<section class="mx-auto max-w-4xl space-y-8">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('marketplaces.conversations.index', $marketplace) }}" wire:navigate variant="ghost">
            Back
        </flux:button>
        <flux:heading size="xl">Conversation about {{ $conversation->listing->title }}</flux:heading>
    </div>

    @if ($this->otherUser)
        <div class="border-b border-gray-200 pb-4">
            <div class="flex items-center gap-4">
                <x-boring-avatar
                    :name="$this->otherUser->name ?? $this->otherUser->email"
                    variant="beam"
                    size="32"
                    class="size-8 shrink-0"
                />
                <div>
                    <flux:text size="sm" variant="muted">Talking with</flux:text>
                    <flux:heading size="md">{{ $this->otherUser->name ?? $this->otherUser->email }}</flux:heading>
                </div>
            </div>
            <div class="mt-4">
                <flux:heading size="lg">Listing details</flux:heading>
                <flux:text>{{ $conversation->listing->description }}</flux:text>
            </div>
        </div>
    @endif

    <div class="space-y-6">
        <div class="space-y-4">
            @if ($conversation->messages->isEmpty())
                <div class="text-center py-12">
                    <flux:text>No messages yet. Start the conversation!</flux:text>
                </div>
            @else
                @foreach ($conversation->messages as $message)
                    <div class="{{ $message->user_id === $this->user->id ? 'ml-auto' : 'mr-auto' }} max-w-2xl">
                        <div class="{{ $message->user_id === $this->user->id ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }} rounded-lg border p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <flux:text size="sm" variant="muted">
                                    {{ $message->user->name ?? $message->user->email }}
                                </flux:text>
                                <flux:text size="sm" variant="muted">
                                    {{ $message->created_at->format('M j, Y g:i A') }}
                                </flux:text>
                            </div>
                            <flux:text>{{ $message->content }}</flux:text>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <form wire:submit="send" class="w-full space-y-4">
            <flux:textarea wire:model="content" label="Your message" rows="4" required />
            <flux:button variant="primary" type="submit">Send</flux:button>
        </form>
    </div>
</section>
