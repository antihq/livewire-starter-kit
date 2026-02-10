<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

new #[Layout('layouts.marketplace')] class extends Component
{
    use WithFilePond;

    public Marketplace $marketplace;

    public $title = '';

    public $description = '';

    public $price = '';

    public $address = '';

    public $latitude = null;

    public $longitude = null;

    public $photos = [];

    public function mount()
    {
        $this->authorize('view', $this->marketplace);
    }

    public function rules(): array
    {
        return [
            'photos' => 'required|array',
            'photos.*' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:12000',
        ];
    }

    public function validateUploadedFile()
    {
        $this->validate();

        return true;
    }

    public function create()
    {
        $this->authorize('create', Listing::class);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'address' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $listing = $this->marketplace->listings()->create([
            'title' => $this->title,
            'description' => $this->description,
            'price' => (int) round($this->price * 100),
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'creator_id' => Auth::id(),
            'team_id' => $this->team->id,
        ]);

        foreach ($this->photos as $photo) {
            $listing
                ->addMedia($photo)
                ->withResponsiveImages()
                ->toMediaCollection();
        }

        return $this->redirectRoute('listings.show', $listing);
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
};
?>

<section class="mx-auto max-w-lg">
    <flux:heading size="xl">New listing</flux:heading>

    <div class="mt-14 space-y-14">
        <form wire:submit="create" class="w-full max-w-lg space-y-8">
            <flux:input wire:model="title" label="Listing title" type="text" required autofocus />
            <flux:editor wire:model="description" label="Description" toolbar="bold italic bullet ordered | link | align" required />
            <flux:input wire:model="price" label="Price" type="number" step="0.01" required />

            <div
                x-data="{
                    query: '',
                    suggestions: [],
                    selectedAddress: @js($address),
                    selectedLat: @js($latitude),
                    selectedLng: @js($longitude),
                    loading: false,
                    debounceTimer: null,

                    async searchAddress() {
                        if (this.query.length < 3) {
                            this.suggestions = [];
                            return;
                        }

                        clearTimeout(this.debounceTimer);
                        this.loading = true;

                        this.debounceTimer = setTimeout(async () => {
                            try {
                                const response = await fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(this.query)}.json?access_token=${encodeURIComponent('{{ config('services.mapbox.token') }}')}&autocomplete=true&limit=5`);
                                const data = await response.json();
                                this.suggestions = data.features || [];
                            } catch (error) {
                                this.suggestions = [];
                            } finally {
                                this.loading = false;
                            }
                        }, 300);
                    },

                    selectAddress(suggestion) {
                        this.selectedAddress = suggestion.place_name;
                        this.selectedLat = suggestion.center[1];
                        this.selectedLng = suggestion.center[0];
                        this.query = suggestion.place_name;
                        this.suggestions = [];
                    },

                    clearAddress() {
                        this.query = '';
                        this.selectedAddress = '';
                        this.selectedLat = null;
                        this.selectedLng = null;
                        this.suggestions = [];
                    }
                }"
                x-modelable="selectedAddress"
                class="relative"
            >
                <flux:input
                    x-model="query"
                    label="Address"
                    type="text"
                    required
                    x-on:input="searchAddress()"
                    placeholder="Start typing to search for an address..."
                    autocomplete="off"
                />

                <div x-show="loading" class="mt-2 text-sm text-gray-500">Loading suggestions...</div>

                <ul
                    x-show="suggestions.length > 0"
                    class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                >
                    <template x-for="suggestion in suggestions" :key="suggestion.id">
                        <li
                            x-on:click="selectAddress(suggestion)"
                            class="relative cursor-pointer select-none py-2 px-3 hover:bg-gray-100 text-gray-900"
                        >
                            <span x-text="suggestion.place_name"></span>
                        </li>
                    </template>
                </ul>

                <input type="hidden" name="address" x-model="selectedAddress">
                <input type="hidden" name="latitude" x-model="selectedLat">
                <input type="hidden" name="longitude" x-model="selectedLng">
            </div>

            <x-filepond::upload wire:model="photos" multiple required />
            <div class="flex justify-end gap-4">
                <flux:button href="{{ route('marketplaces.show', $marketplace) }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">Publish listing</flux:button>
            </div>
        </form>
    </div>
</section>

@assets
    @filepondScripts
@endassets
