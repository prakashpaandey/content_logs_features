<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\MonthlyTargetController;
use App\Http\Controllers\BoostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/verification-pending', function () {
        if (auth()->user()->isActive()) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.verification-pending');
    })->name('verification.pending');

    Route::middleware(['check.status'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/clients-overview', [DashboardController::class, 'overview'])->name('clients.overview');
        Route::resource('clients', ClientController::class);
        Route::resource('contents', ContentController::class);
        Route::resource('boosts', BoostController::class);
        Route::post('monthly-targets/bulk', [MonthlyTargetController::class, 'bulkStore'])->name('monthly-targets.bulk');
        Route::resource('monthly-targets', MonthlyTargetController::class);
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Admin Only Routes
        Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
            // Status toggle
            Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
            // Permissions
            Route::get('/users/{user}/permissions', [\App\Http\Controllers\Admin\UserController::class, 'permissions'])->name('users.permissions');
            Route::post('/users/{user}/permissions', [\App\Http\Controllers\Admin\UserController::class, 'updatePermissions'])->name('users.update-permissions');
            Route::post('/users/{user}/activate', [\App\Http\Controllers\Admin\UserController::class, 'activate'])->name('users.activate');
        });
        // Check Permissions & Client List Hash for Real-time Sync
        Route::get('/api/check-state', function() {
            $user = auth()->user();
            if (!$user->permissions_hash) {
                $user->updatePermissionsHash();
            }

            // Generate a hash of all active client IDs to detect additions/deletions
            $clientsHash = md5(\App\Models\Client::where('status', 'active')->pluck('id')->sort()->join(','));

            return response()->json([
                'permissions_hash' => $user->permissions_hash,
                'clients_hash' => $clientsHash
            ]);
        })->name('api.check-state');
    });
});

require __DIR__.'/auth.php';
