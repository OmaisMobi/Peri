<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;

class SocialLogin extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Google Login';
    protected static ?string $title = 'Login with Google';
    protected static string $view = 'filament.pages.social-login';
    protected static ?int $navigationSort = 9;

    public $settings = [];
    public $data = [];

    public function mount(): void
    {
        $this->settings = Setting::getByType('social_login') ?? [];
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
            Section::make('Settings')->schema([
                TextInput::make('settings.google_client_id')
                    ->label('Google Client ID')
                    ->required(),
                TextInput::make('settings.google_client_secret')
                    ->label('Google Client Secret')
                    ->required(),
            ])->columns(1),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $nestedSettings = $data['settings'] ?? [];
        $this->settings = array_merge($this->settings, $nestedSettings);

        Setting::setByType('social_login', $this->settings);

        // Refresh the data in the form after saving
        $this->settings = Setting::getByType('social_login');
        $this->loadFormData();

        Notification::make()
            ->title('Google Login Settings updated successfully!')
            ->success()
            ->send();
    }
}
