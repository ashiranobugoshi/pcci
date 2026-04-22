<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.home'); // or whatever your homepage view is
})->name('home');

Route::get('/membership', function () {
    return view('landing.membership');
})->name('membership');

Route::get('/about', function () {
    return view('landing.about');
})->name('about');  // <--- This 'name' part is crucial!
    
Route::get('/contact', function () {
    return view('landing.contact');
})->name('contact');

Route::get('/business/{id}', function ($id) {
    return view('landing.MembershipBusinessProfileDetails', compact('id'));
})->name('business.show');

Route::get('/leadership', function () {
    return view('landing.leadership');
})->name('leadership');

Route::get('/event', function () {
    return view('landing.event');
})->name('event');

Route::get('/signup', function () {
    return view('auth.signup'); 
})->name('signup');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::get('/admin/profile', function () {
    return view('admin.profile');
})->name('admin.profile');

Route::get('/admin/users', function () {
    return view('admin.admin-users');
})->name('admin.users');

//treasurer dashboard route
Route::get('/treasurer-dashboard', function () {
    return view('treasurer.dashboard');
})->name('treasurer.dashboard');

Route::put('/treasurer/transactions/{id}', [\App\Http\Controllers\Api\TreasurerProxyController::class, 'updateTransaction'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

Route::delete('/treasurer/transactions/{id}', [\App\Http\Controllers\Api\TreasurerProxyController::class, 'cancelTransaction'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// member dashboard route
Route::get('/member-dashboard', function () {
    return view('member.dashboard');
})->name('member.dashboard');

// --- FRONTEND UI ROUTES FROM TEAM BRANCH ---

Route::get('/members', function () {
    return view('admin.members');
})->name('members');

Route::get('/applicants', function () {
    return view('admin.applicants');
})->name('applicants');

// The detailed Applicant Profile page
Route::get('/applicant/{id}', function ($id) {
    // Simulate different data based on ID for demonstration
    $applicantData = [
        'id' => $id,
        'name' => ($id == 1) ? 'Sarah Geronimo' : 'Bamboo Manalac',
        'email' => ($id == 1) ? 'sarah.g@example.com' : 'bamboo@rock.ph',
        'business_name' => ($id == 1) ? 'Pop Studio Inc.' : 'Rivermaya Solutions',
        'status' => ($id == 1) ? 'Under Review' : 'New Application',
    ];

    return view('admin.applicant-profile', ['applicant' => $applicantData]);
})->name('applicant.profile');


Route::get('/content/board-of-trustees', function () {
    return view('landing.board-of-trustees');
})->name('content.trustees');

Route::get('/content/trustees-admin', function () {
    return view('admin.board-of-trustees');
})->name('content.trustees-admin');

Route::get('/content/activities', function () {
    return view('landing.activities');
})->name('content.activities');

Route::get('/content/event-admin', function () {
    return view('admin.events');
    })->name('content.event-admin');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');