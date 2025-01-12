<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Chats\Conversation;
use App\Livewire\Chats\GroupConversation;
use App\Livewire\Chats\Index;
use App\Livewire\Pages\Announcement;
use App\Livewire\Pages\AnnouncementDeleted;
use App\Livewire\Pages\AnnouncementSingle;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Landing;
use App\Livewire\Pages\Profile;
use App\Livewire\Pages\ProfileInfo;
use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/', Landing::class);
Route::get('/login', Login::class);
Route::get('/register', Register::class);
// Route::fallback(Login::class);

Route::group(['middleware' => ['auth', 'verified', 'role:user|admin']], function () {
    Route::get('/chats', Index::class);
    Route::get('/chats/{userToken}', Conversation::class);
    Route::get('/home', Home::class);
    Route::get('/profile', Profile::class);
    Route::get('/profile-info/{username}', ProfileInfo::class);
    Route::get('/gc/{groupChatToken}', GroupConversation::class);
    Route::get('/announcement', Announcement::class);
    Route::get('/updates/{postTitle}', AnnouncementSingle::class);
    Route::get('/updates/post-not-found-or-deleted/404', AnnouncementDeleted::class);
});

Route::group(['middleware' => ['auth', 'verified', 'role:admin']], function () {
    Route::get('/admin/users', UsersIndex::class);
});
