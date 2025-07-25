<?php

namespace App\Filament\Client\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Pages\Auth\EditProfile as EditProfile;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Get;

class Profile extends EditProfile
{
    use HasCustomLayout;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $isScopedToTenant = false;

    /**
     * Handle record update with profile image management
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Handle profile image deletion if needed
        if (array_key_exists('avatar_url', $data) && $data['avatar_url'] !== $record->avatar_url) {
            $this->deleteOldProfileImage($record);
        }

        $record->update($data);

        return $record;
    }

    /**
     * Delete the old profile image from storage
     */
    protected function deleteOldProfileImage(Model $record): void
    {
        if ($record->avatar_url && Storage::disk($this->getProfileImageDisk())->exists($record->avatar_url)) {
            Storage::disk($this->getProfileImageDisk())->delete($record->avatar_url);
        }
    }

    /**
     * Get the disk name for profile image storage
     */
    protected function getProfileImageDisk(): string
    {
        return config('filament.default_filesystem_disk', 'public');
    }

    /**
     * Get the directory for profile image storage
     */
    protected function getProfileImageDirectory(): string
    {
        return 'profile-images';
    }

    /**
     * Profile image upload component
     */
    protected function getProfileImageFormComponent(): Component
    {
        return FileUpload::make('avatar_url')
            ->label(false)
            ->image()
            ->avatar()
            ->disk($this->getProfileImageDisk())
            ->directory($this->getProfileImageDirectory())
            ->visibility('public')
            ->imageResizeMode('cover')
            ->imageCropAspectRatio('1:1')
            ->imageResizeTargetWidth('300')
            ->imageResizeTargetHeight('300')
            ->maxSize(2048) // 2MB
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->deletable()
            ->downloadable()
            ->previewable();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->disabled(fn(): bool => !Auth::user()->hasRole('Admin'))
            ->unique(ignoreRecord: true);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::pages/auth/edit-profile.form.name.label'))
            ->required()
            ->maxLength(255)
            ->disabled(fn(): bool => !Auth::user()->hasRole('Admin'))
            ->autofocus();
    }
    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->visible(fn(Get $get): bool => filled($get('password')))
            ->required()
            ->dehydrated(false);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Fieldset::make("Details")
                            ->schema([
                                $this->getProfileImageFormComponent()
                                    ->extraAttributes([
                                        'style' => 'display: flex; justify-content: center; align-items: center;',
                                        'class' => 'w-full text-center'
                                    ])
                                    ->columnSpanFull(),
                                $this->getNameFormComponent(),
                                $this->getEmailFormComponent(),
                                $this->getPasswordFormComponent(),
                                $this->getPasswordConfirmationFormComponent(),
                            ])
                            ->columns(1)
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }
}
