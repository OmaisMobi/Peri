<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use App\Models\PrivacyPolicy;
use Filament\Notifications\Notification;

class PrivacyPolicyPage extends Page
{
    protected static string $view = 'filament.pages.privacy-policy';
    protected static ?string $title = 'Privacy Policy';
    protected static ?string $navigationLabel = 'Privacy Policy';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationGroup = 'Website Pages';

    public $form_data = [
        'content' => '',
    ];

    public function mount()
    {
        $policy = PrivacyPolicy::latest()->first();
        if ($policy) {
            $this->form_data['content'] = $policy->content;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('uploads/privacy-policy')
                    ->fileAttachmentsVisibility('public')
                    ->required(),
            ])
            ->statePath('form_data');
    }

    public function save()
    {
        PrivacyPolicy::updateOrCreate(
            ['id' => PrivacyPolicy::latest('id')->value('id')],
            ['content' => $this->form_data['content']]
        );

        Notification::make()
            ->title('Success')
            ->body('Privacy policy saved successfully.')
            ->success()
            ->send();
    }
}
