<?php

use App\Events\MessageSent;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Chats\Conversation;
use App\Livewire\Chats\GroupConversation;
use App\Livewire\Chats\Index;
use App\Livewire\Pages\Announcement;
use App\Livewire\Pages\AnnouncementSingle;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Landing;
use App\Livewire\Pages\Profile;
use App\Livewire\Pages\ProfileInfo;
use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', Landing::class);
Route::get('/login', Login::class);
Route::get('/register', Register::class);
Route::fallback(Login::class);

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/chats', Index::class);
    Route::get('/chats/{userToken}', Conversation::class);
    Route::get('/home', Home::class);
    Route::get('/users', UsersIndex::class);
    Route::get('/profile', Profile::class);
    Route::get('/profile-info/{username}', ProfileInfo::class);
    Route::get('/gc/{groupChatToken}', GroupConversation::class);
    Route::get('/announcement', Announcement::class);
    Route::get('/updates/{postTitle}', AnnouncementSingle::class);
});
