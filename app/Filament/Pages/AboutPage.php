<?php

namespace App\Filament\Pages;

use App\Models\About;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Components\{
    TextInput,
    FileUpload,
    Section,
    Grid
};
use Filament\Notifications\Notification;

class AboutPage extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationGroup = 'Website Pages';
    protected static ?string $navigationLabel = 'About';
    protected static string $view = 'filament.pages.about';
    protected static ?int $navigationSort = 2;

    public $aboutData = [
        'section_image' => '',
        'product_image' => '',
        'sections' => [
            'group1' => ['title' => '', 'description' => '', 'icon' => ''],
            'group2' => ['title' => '', 'description' => '', 'icon' => ''],
            'group3' => ['title' => '', 'description' => '', 'icon' => ''],
        ],
    ];

    public function mount(): void
    {
        $about = About::first();
        $this->aboutData = [
            'section_image' => $about?->section_image ?? '',
            'product_image' => $about?->product_image ?? '',
            'sections' => $about?->value ?? [
                'group1' => ['title' => '', 'description' => '', 'icon' => ''],
                'group2' => ['title' => '', 'description' => '', 'icon' => ''],
                'group3' => ['title' => '', 'description' => '', 'icon' => ''],
            ],
        ];
        $this->form->fill($this->aboutData);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('Section Images')
                    ->schema([
                        FileUpload::make('section_image')
                            ->label('Upload Background Image')
                            ->directory('uploads/about')
                            ->image()
                            ->preserveFilenames(),
                        FileUpload::make('product_image')
                            ->label('Upload Product Image')
                            ->directory('uploads/about')
                            ->image()
                            ->preserveFilenames(),
                    ])
                    ->columns(2),
                Section::make('Section Data')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Group 1
                                TextInput::make('sections.group1.title')
                                    ->label('1st Slot Title')
                                    ->required(),
                                TextInput::make('sections.group1.description')
                                    ->label('Description')
                                    ->required(),
                                TextInput::make('sections.group1.icon')
                                    ->label('Icon Class (e.g., bi-alarm)')
                                    ->hint("Bootstrap icons only")
                                    ->required(),
                                // Group 2
                                TextInput::make('sections.group2.title')
                                    ->label('2nd Slot Title')
                                    ->required(),
                                TextInput::make('sections.group2.description')
                                    ->label('Description')
                                    ->required(),
                                TextInput::make('sections.group2.icon')
                                    ->label('Icon Class (e.g., bi-alarm)')
                                    ->required(),
                                // Group 3
                                TextInput::make('sections.group3.title')
                                    ->label('3rd Slot Title')
                                    ->required(),
                                TextInput::make('sections.group3.description')
                                    ->label('Description')
                                    ->required(),
                                TextInput::make('sections.group3.icon')
                                    ->label('Icon Class (e.g., bi-alarm)')
                                    ->required(),
                            ]),
                    ]),
            ])
            ->statePath('aboutData');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Merge the nested sections data into our aboutData
        $nestedSections = $data['sections'] ?? [];
        $this->aboutData['sections'] = array_merge($this->aboutData['sections'], $nestedSections);

        // Update both image fields, even if their values are null (i.e. image removed)
        $this->aboutData['section_image'] = $data['section_image'] ?? null;
        $this->aboutData['product_image'] = $data['product_image'] ?? null;

        $about = About::first() ?? new About();
        $about->section_image = $this->aboutData['section_image'];
        $about->product_image = $this->aboutData['product_image'];
        $about->value = $this->aboutData['sections'];
        $about->save();

        // Refresh the form state after saving
        $about = About::first();
        $this->aboutData = [
            'section_image' => $about->section_image,
            'product_image' => $about->product_image,
            'sections' => $about->value,
        ];
        $this->form->fill($this->aboutData);

        Notification::make()
            ->title('About section updated successfully!')
            ->success()
            ->send();
    }
}
