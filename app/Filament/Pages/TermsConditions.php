<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use App\Models\Term;
use Filament\Notifications\Notification;

class TermsConditions extends Page
{
    protected static string $view = 'filament.pages.terms-conditions';
    protected static ?string $title = 'Terms & Conditions';
    protected static ?string $navigationLabel = 'Terms & Conditions';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationGroup = 'Website Pages';

    public $form_data = [
        'content' => '',
    ];

    public function mount()
    {
        $terms = Term::latest()->first();
        if ($terms) {
            $this->form_data['content'] = $terms->content;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('uploads/terms-conditions')
                    ->fileAttachmentsVisibility('public')
                    ->required(),
            ])
            ->statePath('form_data');
    }

    public function save()
    {
        Term::updateOrCreate(
            ['id' => Term::latest('id')->value('id')],
            ['content' => $this->form_data['content']]
        );

        Notification::make()
            ->title('Success')
            ->body('Terms & Conditions saved successfully.')
            ->success()
            ->send();
    }
}
