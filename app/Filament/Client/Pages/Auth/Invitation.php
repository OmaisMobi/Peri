<?php

namespace App\Filament\Client\Pages\Auth;

use Filament\Pages\SimplePage;
use Illuminate\Support\Facades\Request;
use App\Models\Invitation as InvitationModel;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Invitation extends SimplePage
{
    use HasCustomLayout;
    protected static string $view = 'filament.client.pages.auth.invitation';
    protected static ?string $routeName = 'client.auth.invitation';
    protected static bool $shouldRegisterNavigation = false;
    protected static bool $isScopedToTenant = false;
    public ?array $data = [];
    public ?InvitationModel $invitation = null;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }
        $expires = request()->query('expires');
        $signature = request()->query('signature');

        $originalUrl = URL::to(request()->path()) . '?expires=' . $expires;
        $expectedSignature = hash_hmac('sha256', $originalUrl, config('app.key'));

        if (! hash_equals($expectedSignature, $signature) || now()->timestamp > (int) $expires) {
            abort(403, 'Invalid or expired invitation link.');
        }
        $token = request()->route('token');
        $this->invitation = InvitationModel::where('token', $token)->firstOrFail();
        setPermissionsTeamId($this->invitation->team_id);
        $user = User::where('email', $this->invitation->email)->first();
        $team = Team::find($this->invitation->team_id);

        if ($user) {
            $role = Role::find($this->invitation->role);
            $user->assignRole($role);
            $team->members()->attach($user);
            $this->invitation->delete();
            Filament::auth()->login($user, true);
            $this->redirect(Filament::getUrl(), navigate: true);
            return;
        }
        $this->data['email'] = $this->invitation->email;
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::pages/auth/register.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->autocomplete()
            ->autofocus()
            ->readOnly()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }
    public function getTitle(): string | Htmlable
    {
        return "Create Profile";
    }

    public function getHeading(): string | Htmlable
    {
        return "Create Profile";
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
        ];
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label('Submit')
            ->submit('register');
    }

    public function register(): void
    {
        $data = $this->form->getState();
        $data = $this->mutateFormDataBeforeRegister($data);
        $userClass = $this->getUserModel();
        $user = new $userClass();
        $user->name = $data['name'];
        $user->email = $this->invitation->email;
        $user->password = $data['password'];
        $user->save();
        setPermissionsTeamId($this->invitation->team_id);
        $role = Role::find($this->invitation->role);
        $user->assignRole($role);
        $team = Team::find($this->invitation->team_id);
        $team->members()->attach($user);
        $this->invitation->delete();
        Filament::auth()->login($user, true);
        $this->redirect(Filament::getUrl(), navigate: true);
    }

    protected function getUserModel(): string
    {
        if (isset($this->userModel)) {
            return $this->userModel;
        }
        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();
        /** @var EloquentUserProvider $provider */
        $provider = $authGuard->getProvider();

        return $this->userModel = $provider->getModel();
    }
    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        return $this->getUserModel()::create($data);
    }
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        return $data;
    }
    public static function getRouteName()
    {
        return static::$routeName;
    }
    public static function getUrl(array $parameters = []): string
    {
        return route(static::getRouteName(), $parameters);
    }
}
