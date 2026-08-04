<?php

use App\Http\Controllers\Api\BridgeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bridge routes
|--------------------------------------------------------------------------
|
| The People-ERP beneficiary portal (a separate MERN application on its own
| database) is now the public front door for scholarship applications. It
| pushes every submitted application here so it keeps appearing in this
| admin dashboard alongside the ones entered through /application.
|
| Every route is authenticated by an HMAC of the raw request body rather
| than by session, because the caller is a server, not a browser. There is
| no user context and nothing here is reachable from the public site.
|
*/

Route::middleware('bridge.signature')->prefix('v1/bridge')->group(function () {
    Route::get('/master', [BridgeController::class, 'master'])->name('bridge-master');
    Route::post('/locations/resolve', [BridgeController::class, 'resolveLocations'])->name('bridge-resolve-locations');
    Route::post('/applications', [BridgeController::class, 'storeApplication'])->name('bridge-store-application');
});
