@php
    /** @var \App\Models\Event $event */
@endphp
{{-- Inline styles: Filament's prebuilt stylesheet only ships the utilities it uses itself. --}}
<div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; text-align: center;">
    @unless ($event->is_published)
        <x-filament::badge color="warning">
            {{ __('admin.qr.unpublished_warning') }}
        </x-filament::badge>
    @endunless

    <p style="font-size: 0.875rem; opacity: 0.75;">{{ __('admin.qr.description') }}</p>

    <div style="width: 16rem; max-width: 100%; padding: 0.5rem; background: #fff; border-radius: 0.5rem;">
        <img
            src="data:image/svg+xml;base64,{{ base64_encode($svg) }}"
            alt="QR"
            style="display: block; width: 100%; height: auto;"
        >
    </div>

    <x-filament::link :href="$url" target="_blank" style="word-break: break-all;">
        {{ $url }}
    </x-filament::link>

    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem;">
        <x-filament::button tag="a" :href="route('admin.events.qr', ['event' => $event, 'format' => 'png'])" icon="heroicon-o-arrow-down-tray">
            {{ __('admin.qr.download_png') }}
        </x-filament::button>
        <x-filament::button tag="a" color="gray" :href="route('admin.events.qr', ['event' => $event, 'format' => 'svg'])" icon="heroicon-o-arrow-down-tray">
            {{ __('admin.qr.download_svg') }}
        </x-filament::button>
    </div>
</div>
