<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use App\Models\Guide;
use Filament\Notifications\Notification;

class UserGuide extends Page
{
    protected static string $view = 'filament.pages.user-guide';
    protected static ?string $title = 'User Guide';
    protected static ?string $navigationLabel = 'User Guide';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Website Pages';

    public $form_data = [
        'content' => '',
    ];

    public function mount()
    {
        $guide = Guide::latest()->first();
        if ($guide) {
            $this->form_data['content'] = $guide->content;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('uploads/guide')
                    ->fileAttachmentsVisibility('public')
                    ->required(),
            ])
            ->statePath('form_data');
    }

    public function save()
    {
        Guide::updateOrCreate(
            ['id' => Guide::latest('id')->value('id')],
            ['content' => $this->form_data['content']]
        );

        Notification::make()
            ->title('Success')
            ->body('Guide content saved successfully.')
            ->success()
            ->send();
    }
}
