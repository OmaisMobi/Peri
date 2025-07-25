<ul>
    <x-filament-panels::sidebar.item :icon="$icon" :url="$url" :should-open-url-in-new-tab="$shouldOpenUrlInNewTab">
        <x-filament::button class="" style="box-shadow: none; background: none; padding:0;" color="gray"
            icon-size="lg" tag="span">
            {{ $label }}
        </x-filament::button>
    </x-filament-panels::sidebar.item>
</ul>
