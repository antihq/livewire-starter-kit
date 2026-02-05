<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\RoutePath;

Route::redirect('/', 'dashboard')->name('home');

Route::view('site.webmanifest', 'site-webmanifest');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::livewire('teams/create', 'pages::teams.create')->name('teams.create');
    Route::livewire('teams/{team}', 'pages::teams.show')->name('teams.edit');
    Route::livewire('teams/{team}/members', 'pages::teams.members.index')->name('teams.members.index');

    Route::livewire('ssh-keys', 'pages::ssh-keys.index')->name('ssh-keys.index');
    Route::livewire('ssh-keys/create', 'pages::ssh-keys.create')->name('ssh-keys.create');
    Route::livewire('ssh-keys/{sshKey}/edit', 'pages::ssh-keys.edit')->name('ssh-keys.edit');

    Route::livewire('servers', 'pages::servers.index')->name('servers.index');
    Route::livewire('servers/create', 'pages::servers.create')->name('servers.create');
    Route::livewire('servers/{server}', 'pages::servers.show')->name('servers.show');
    Route::livewire('servers/{server}/sites/create', 'pages::servers.sites.create')->name('servers.sites.create');
    Route::livewire('servers/{server}/sites', 'pages::servers.sites.index')->name('servers.sites.index');

    Route::livewire('servers/{server}/databases/create', 'pages::servers.databases.create')->name('servers.databases.create');
    Route::livewire('servers/{server}/databases', 'pages::servers.databases.index')->name('servers.databases.index');

    Route::livewire('servers/{server}/database-users/create', 'pages::servers.database-users.create')->name('servers.database-users.create');
    Route::livewire('servers/{server}/database-users', 'pages::servers.database-users.index')->name('servers.database-users.index');

    Route::livewire('servers/{server}/cronjobs/create', 'pages::servers.cronjobs.create')->name('servers.cronjobs.create');
    Route::livewire('servers/{server}/cronjobs', 'pages::servers.cronjobs.index')->name('servers.cronjobs.index');
    Route::livewire('cronjobs/{cronjob}/edit', 'pages::cronjobs.edit')->name('cronjobs.edit');

    Route::livewire('servers/{server}/daemons/create', 'pages::servers.daemons.create')->name('servers.daemons.create');
    Route::livewire('servers/{server}/daemons', 'pages::servers.daemons.index')->name('servers.daemons.index');
    Route::livewire('daemons/{daemon}/edit', 'pages::daemons.edit')->name('daemons.edit');

    Route::livewire('sites/{site}', 'pages::sites.show')->name('sites.show');

    Route::redirect('account', 'settings/profile');

    Route::livewire('account/profile', 'pages::account.profile')->name('profile.edit');
    Route::livewire('account/password', 'pages::account.password')->name('user-password.edit');
    Route::livewire('account/appearance', 'pages::account.appearance')->name('appearance.edit');
    Route::livewire('account/devices', 'pages::account.devices')->middleware(['password.confirm'])->name('devices.create');

    Route::livewire('account/two-factor', 'pages::account.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::get('device-login/{user}', function (Request $request, User $user) {
    if (! $request->hasValidSignature()) {
        abort(401);
    }

    Auth::login($user);

    return redirect()->route('dashboard');
})->name('device-login')->middleware('signed');

Route::get(RoutePath::for('password.reset', '/reset-password/{token}'), [NewPasswordController::class, 'create'])
    ->name('password.reset');

Route::post(RoutePath::for('password.update', '/reset-password'), [NewPasswordController::class, 'store'])
    ->name('password.update');
