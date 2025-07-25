<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;

class Recaptcha extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Recaptcha';
    protected static string $view = 'filament.pages.recaptcha';
    protected static ?int $navigationSort = 10;

    public $settings = [];
    public $data = [];

    public function mount(): void
    {
        $this->settings = Setting::getByType('recaptcha') ?? [];
        $this->loadFormData();
    }

    protected function loadFormData(): void
    {
        $this->form->fill([
            'settings' => $this->settings,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Google Recaptcha Settings')->schema([
                TextInput::make('settings.site_key')
                    ->label('Site Key')
                    ->required(),
                TextInput::make('settings.secret_key')
                    ->label('Secret Key')
                    ->required(),
            ])->columns(1),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $nestedSettings = $data['settings'] ?? [];
        $this->settings = array_merge($this->settings, $nestedSettings);

        Setting::setByType('recaptcha', $this->settings);

        // Refresh the data in the form after saving
        $this->settings = Setting::getByType('recaptcha');
        $this->loadFormData();

        Notification::make()
            ->title('Recaptcha Settings updated successfully!')
            ->success()
            ->send();
    }
}
