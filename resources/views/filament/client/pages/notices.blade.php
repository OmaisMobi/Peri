@php
    use Illuminate\Support\Facades\Storage;
    use Filament\Facades\Filament;
    if (Auth::user()->hasRole('Admin')) {
        $notices = Filament::getTenant()->notices()->where('is_active', true)->get();
    }else{
        $notices = Filament::getTenant()->notices()->where('is_active', true)->where('name', '!=', 'Subscription Expiring Soon')->get();
    }
@endphp

<div class="sticky top-0 z-999">
    @foreach ($notices as $notice)
        @php
            $canBeClosed = $notice->content['can_be_closed_by_user'] ?? false;
            $icon = $notice->icon ?? 'heroicon-m-megaphone';
            $iconColor = $notice->content['IconColor'] ?? '#FFFFFF';
            $textColor = $notice->content['TextColor'] ?? '#FFFFFF';
            $backgroundColor = $notice->content['BackgroundColor'] ?? '#D97706';
            $contentType = $notice->content['type'] ?? 'text';
            $linkUrl = $notice->content['link_url'] ?? '#';
            $documentPath = $notice->content['document'] ?? null;
            $hasTextContent = $contentType === 'text' && !empty($notice->content['body']);
            $windowSettings = 'width=800,height=600,scrollbars=yes,resizable=yes,left=100,top=100';
        @endphp

        <div x-data="{
            show: true,
            storageKey: 'my-notices::closed',
            noticeId: '{{ $notice->id }}',
            init() { this.hasBeenClosedByUser(); },
            close(e) {
                e.stopPropagation();
                this.show = false;
                let stored = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                stored.push(this.noticeId);
                localStorage.setItem(this.storageKey, JSON.stringify(stored));
            },
            hasBeenClosedByUser() {
                let stored = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                this.show = !stored.includes(this.noticeId);
            },
            handleBannerClick(e) {
                if (e.target.closest('.close-button')) {
                    return; // Don't do anything if we clicked the close button
                }
        
                @if ($contentType === 'link') window.open('{{ $linkUrl }}', 'noticeWindow', '{{ $windowSettings }}');
                @elseif ($contentType === 'file' && $documentPath)
                    window.open('{{ Storage::url($documentPath) }}', 'noticeWindow', '{{ $windowSettings }}');
                @elseif ($hasTextContent)
                    $dispatch('open-modal', { id: 'notice-content-{{ $notice->id }}' }); @endif
            }
        }" x-show="show" x-cloak>
            <div @click="handleBannerClick($event)" style="cursor: pointer; background-color: {{ $backgroundColor }};"
                class="p-4 mb-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <x-filament::icon alias="notice::icon" :icon="$icon" style="color: {{ $iconColor }}"
                            class="h-6 w-6 mr-2 text-white" />
                        <div style="color: {{ $textColor }}; text-decoration: underline;">
                            {!! $notice->name ?? '' !!}
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Close Icon --}}
                        @if ($canBeClosed)
                            <x-filament::icon @click="close($event)" alias="notice::close" icon="heroicon-m-x-mark"
                                class="h-6 w-6 text-white cursor-pointer hover:opacity-75 close-button" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Filament Modals (outside the loop to avoid nesting issues) --}}
@foreach ($notices as $notice)
    @php
        $contentType = $notice->content['type'] ?? 'text';
        $hasTextContent = $contentType === 'text' && !empty($notice->content['body']);
    @endphp

    @if ($hasTextContent)
        <x-filament::modal id="notice-content-{{ $notice->id }}" width="2xl">
            <x-slot name="heading">
                {{ $notice->name ?? 'Notice Content' }}
            </x-slot>

            <div class="prose max-h-[60vh] overflow-y-auto text-gray-700">
                {!! $notice->content['body'] ?? '' !!}
            </div>

            <x-slot name="footer">
                <x-filament::button @click="$dispatch('close-modal', { id: 'notice-content-{{ $notice->id }}' })"
                    color="secondary">
                    Close
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    @endif
@endforeach
