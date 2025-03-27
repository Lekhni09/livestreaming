<?php

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/joinMeeting/{url?}', [App\Http\Controllers\MeetingController::class, 'joinMeeting'])->name('joinMeeting');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Auth::routes();
Route::get('/home', [App\Http\Controllers\MeetingController::class, 'meetingUser'])->name('meetingUser');
Route::get('/createMeeting', [App\Http\Controllers\MeetingController::class, 'createMeeting'])->name('createMeeting');
Route::get('/saveUserName', [App\Http\Controllers\MeetingController::class, 'saveUserName'])->name('saveUserName');
Route::get('/meetingApprove', [App\Http\Controllers\MeetingController::class, 'meetingApprove'])->name('meetingApprove');
