<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;

class SEO extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'SEO';
    protected static string $view = 'filament.pages.seo';
    protected static ?int $navigationSort = 8;

    public $settings = [];
    public $data = [];

    public function mount(): void
    {
        $this->settings = Setting::getByType('SEO') ?? [];
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
            Section::make('SEO Settings')->schema([
                TextInput::make('settings.meta_title')
                    ->label('Meta Title')
                    ->required(),

                Textarea::make('settings.meta_description')
                    ->label('Meta Description')
                    ->required()
                    ->rows(3),

                Textarea::make('settings.meta_keywords')
                    ->label('Meta Keywords')
                    ->required()
                    ->rows(2)
                    ->helperText('Comma-separated keywords'),
            ])->columns(1),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $nestedSettings = $data['settings'] ?? [];
        $this->settings = array_merge($this->settings, $nestedSettings);

        Setting::setByType('SEO', $this->settings);

        // Refresh the data in the form after saving
        $this->settings = Setting::getByType('SEO');
        $this->loadFormData();

        Notification::make()
            ->title('SEO Settings updated successfully!')
            ->success()
            ->send();
    }
}
