<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WebDavController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
$verbs = [
    'GET',
    'HEAD',
    'POST',
    'PUT',
    'PATCH',
    'DELETE',
    'PROPFIND',
    'PROPPATCH',
    'MKCOL',
    'COPY',
    'MOVE',
    'LOCK',
    'UNLOCK',
    'OPTIONS',
    'REPORT',
];

Router::$verbs = array_merge(Router::$verbs, $verbs);

if (env('APP_ENV') == 'production') {
    \Illuminate\Support\Facades\URL::forceScheme("https");
}

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'can:manage-users'])->group(function () {
    Route::get('/users', [UsersController::class, 'show'])->name('users');
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::put('/users/reset-password/{id}/', [NewPasswordController::class, 'adminPasswordReset'])
        ->name("password.admin-reset");

    Route::resource('projects', ProjectController::class);
    Route::post('/project-user', [ProjectUserController::class, 'attach'])->name('projects.add-user');
    Route::delete('/project-user', [ProjectUserController::class, 'detach'])->name('projects.remove-user');
});

Route::any('/webdav/{projectSlug}/{path?}', WebDavController::class)
    ->where('path', '(.)*')
    ->middleware('auth.basic');

require __DIR__ . '/auth.php';
