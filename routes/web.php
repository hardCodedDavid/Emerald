<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Investment;
use App\Transaction;
use App\User;
use App\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::get('/register', function() {
    return redirect('/login');
});

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/batch-payouts', 'BatchPayoutController@index');

Route::get('/admin/forget-password', 'Auth\AdminForgotPasswordController@showLinkRequestForm');
Route::post('/email/admin', 'Auth\AdminForgotPasswordController@sendResetLinkEmail');
Route::get('/reset/admin/{token}', 'Auth\AdminResetPasswordController@showResetForm');
Route::post('/reset/admin', 'Auth\AdminResetPasswordController@reset');

//Route::get('/admin', 'HomeController@index')->name('home');

Route::get('admin/login', 'Auth\AdminLoginController@showLoginForm');
Route::post('admin/login', 'Auth\AdminLoginController@login')->name('admin.login');

Route::group(['prefix' => 'admin','middleware' => 'assign.guard:admin,admin/login'],function(){

	Route::get('/', 'AdminController@index')->name('admin.home');
	Route::get('/home', 'AdminController@index');

    Route::get('users/{user}/investments', 'AdminController@showUserInvestments')->name('admin.user.investments');
    Route::get('users/{user}/investments/long', 'AdminController@showUserLongInvestments')->name('admin.user.investments.long');
    Route::get('users/{user}/wallets', 'AdminController@showUserWallet')->name('admin.user.wallets');
    Route::get('users/{user}/transactions', 'AdminController@showUserTransaction')->name('admin.user.transaction');


    Route::get('/farmlist/long', 'Admin\FarmListController@longIndex');
    Route::get('/farmlist/long/add', 'Admin\FarmListController@addLong');
    Route::post('/farmlist/long/add', 'Admin\FarmListController@addLongPost')->name('farmlist.add.long');
    Route::get('/farmlist/short', 'Admin\FarmListController@shortIndex');
    Route::get('/farmlist/short/add', 'Admin\FarmListController@addShort');
    Route::post('/farmlist/add', 'AdminController@addFarmlistPost')->name('farmlist.add');

    Route::get('/users', 'AdminController@users');
    Route::get('/users/verified', 'AdminController@verifiedUsers');
    Route::get('/users/unverified', 'AdminController@unverifiedUsers');
    Route::get('/users/view/{id}', 'AdminController@viewUser');

    Route::get("/bookings", 'Admin\BookingController@index')->name('admin.bookings');
    Route::get("/farmlist/{farmlist}/bookings", 'Admin\BookingController@show')->name('admin.bookings.show');
    Route::get("/bookings/{booking}/approve", 'Admin\BookingController@approve');
    Route::get("/bookings/{booking}/decline", 'Admin\BookingController@decline');

    Route::get("/transactions/deposits/approve/{id}", 'AdminController@approveDeposits');
    Route::get("/transactions/payouts/approve/{id}", 'AdminController@approvePayouts');

    Route::get("/news", 'AdminController@news');
    Route::get("/news/add", 'AdminController@addNews');
    Route::post("/news/add", 'AdminController@addNewsPost')->name('news.add');
    Route::get("/news/edit/{slug}", 'AdminController@editNews');
    Route::post("/news/edit", 'AdminController@editNewsPost')->name('news.edit');

    Route::get("/transactions", 'AdminController@transactions');

    Route::get("/transactions/investments/short", 'AdminController@shortInvestments')->name('admin.investments.short');
    Route::get("/transactions/investments/long", 'AdminController@longInvestments')->name('admin.investments.long');
    Route::get("/transactions/payouts", 'AdminController@payouts')->name('admin.payouts');
    Route::get("/transactions/deposits", 'AdminController@deposits')->name('admin.deposits');
    Route::get("/transactions/investments", 'AdminController@investments');
    Route::get("/transactions/referrals", 'AdminController@referrals');
    Route::get('/transactions/paystack', 'AdminController@paystack')->name('admin.paystack');
    Route::get("/transactions/payouts/request", 'AdminController@payoutsRequest');
    Route::get("/transactions/{type}/download", 'AdminController@downloadTransactions')->name('download.transactions');
    Route::get("/wallets", 'AdminController@wallets');

    Route::get("/profile", 'AdminController@profile');
    Route::post("/profile", 'AdminController@profilePost')->name('admin.profile.edit');

    Route::get('/logout', 'AdminController@logout');

    Route::get("/transactions/investments/long/{id}/show", 'AdminController@showLongInvestments')->name('long-investment.show.admin');


    Route::group(['middleware' => 'manageradmin.only'], function(){
        Route::get('/farmlist/edit/{slug}', 'AdminController@editFarmlist');
        Route::get('/farmlist/long/{slug}/edit', 'Admin\FarmListController@editLongFarmlist');
        Route::post('/farmlist/edit/long', 'Admin\FarmListController@editLongFarmlistPost')->name('farmlist.long.edit');
        Route::post('/farmlist/edit/update', 'AdminController@editFarmlistPost')->name('farmlist.edit');

        Route::post("/transactions/deposits/add", 'AdminController@depositForUser')->name('admin.deposit.user');
        Route::post("/transactions/payouts/add", 'AdminController@withdrawForUser')->name('admin.withdraw.user');
        Route::post("/transactions/transfer/add", 'AdminController@transferForUser')->name('admin.transfer.user');

        Route::post("/farm/add", 'AdminController@investForUser')->name('admin.farmlist.store');
        Route::post("/farm/long-invest/add", 'AdminController@investLongForUser')->name('admin.long-invest.store');
        Route::post("/booking/add", 'Admin\BookingController@bookForUser')->name('admin.bookings.store');

        Route::get('/payouts', 'Admin\PayoutController@index');
        Route::get('/payouts/{category}/farms', 'Admin\PayoutController@showFarmsByCategory');
        Route::get('/payouts/{category}/farms/{farmlist}/payouts', 'Admin\PayoutController@showInvestmentsByCategoryAndFarm');
        Route::get('/payouts/{slug}/payout', 'Admin\PayoutController@payoutInvestment')->name('payout.investment');
        Route::get('/payouts/{slug}/payout/all', 'Admin\PayoutController@payoutAllInvestments')->name('payout.investment.all');

        Route::get('/batch-payouts', 'Admin\BatchPayoutController@index');
        Route::put('/batch-payouts/{batchPayout}/update', 'Admin\BatchPayoutController@update');
        Route::delete('/batch-payouts/{batchPayout}/delete', 'Admin\BatchPayoutController@destroy');
        Route::post('/batch-payouts/upload', 'Admin\BatchPayoutController@upload');

        Route::get('/payouts/long', 'Admin\PayoutController@longPayout');
        Route::get('/payouts/long/{id}', 'Admin\PayoutController@longPayoutShow');


        Route::get('/pay/wallet/{id}', 'AdminController@payOutWallet');
        Route::post('/pay/wallet', 'AdminController@payOutWalletPost')->name('wallet.pay');
        Route::get('/farmlist/{category}/farms', 'AdminController@farmlistByCategory');

    });

    Route::group(['middleware' => 'superadmin.only'], function() {
        Route::get('/farmlist/delete/{slug}', 'AdminController@deleteFarmlist');
        Route::get('/farmlist/long/{slug}/delete', 'Admin\FarmlistController@deleteLongFarmlist');

        Route::get('/admins', 'AdminController@admins')->name('admin.admin.users');
        Route::get('/admins/create', 'AdminController@adminsCreate')->name('admin.admin.create');
        Route::post('/admins', 'AdminController@adminsCreatePost')->name('admin.admin.store');
        Route::get('/roles/{admin}/super', 'AdminController@makeSuperAdmin');
        Route::get('/roles/{admin}/manager', 'AdminController@makeManagerAdmin');
        Route::get('/roles/{admin}/staff', 'AdminController@makeStaffAdmin');
        Route::get("/transactions/deposits/decline/{id}", 'AdminController@declineDeposits');
        Route::get("/transactions/payouts/decline/{id}", 'AdminController@declinePayouts');
        Route::get('/users/delete/{id}', 'AdminController@deleteUser');
        Route::get("/news/delete/{slug}", 'AdminController@deleteNews');
        Route::get('/admins/{admin}/delete', 'AdminController@deleteAdmin')->name('admin.delete');

    });

//	Route::get('/packages/add', 'AdminController@addPackages');
//	Route::post('/packages/add', 'AdminController@addPackagesPost')->name('package.add');
//	Route::get('/packages', 'AdminController@packages');
//	Route::get('/packages/delete/{slug}', 'AdminController@deletePackage');
//	Route::get('/packages/edit/{slug}', 'AdminController@editPackage');
//	Route::post('/packages/edit', 'AdminController@editPackagePost')->name('package.edit');


    Route::get("/newsletter", 'AdminController@newsletter')->name('news.letter');
	Route::post("/newsletter", 'AdminController@newsletterSend')->name('newsletter.send');


    Route::get('/categories', 'AdminController@categories');
    Route::get('/categories/add', 'AdminController@addCategories');
    Route::post('/categories/add', 'AdminController@addCategoriesPost')->name('category.add');
    Route::get('/categories/{category}/delete', 'AdminController@deleteCategories');
    Route::get('/categories/{category}/edit', 'AdminController@editCategory');
    Route::post('/categories/edit', 'AdminController@editCategoryPost')->name('category.edit');


});

// utilitis
Route::post("/loadBatchPayouts", 'Admin\BatchPayoutController@loadBatchPayouts');
Route::post("/loadWallets", 'AdminController@loadWallets');
Route::post("/loadTransactions", 'AdminController@loadTransactions');
Route::post("/loadTransactionInvestment", 'AdminController@loadTransactionInvestments');
Route::post("/loadInvestments", 'AdminController@loadInvestments');
Route::post("/loadLongInvestments", 'AdminController@loadLongInvestments');
Route::post("/loadFarms", 'AdminController@loadFarms');
Route::post("/loadLongFarms", 'Admin\FarmListController@loadLongFarms');
Route::post("/loadPayout", 'AdminController@loadPayout');
Route::post("/loadBooking", 'Admin\BookingController@loadBooking');
Route::post("/loadPayouts", 'AdminController@loadPayouts');
Route::post("/loadPaystack", 'AdminController@loadPaystack');
Route::post("/loadDeposits", 'AdminController@loadDeposits');
Route::post("/loadAllUser", 'AdminController@loadAllUser');
Route::post("/loadVerifiedUser", 'AdminController@loadVerifiedUser');
Route::post("/loadUnverifiedUser", 'AdminController@loadUnverifiedUser');
Route::post("/loadFarms/{type}", 'HomeController@loadFarms');
Route::post("/loadLongFarm", 'FarmlistController@loadLongFarms');
Route::post("/loadAllAdmin", 'AdminController@loadAllAdmin');
Route::get("/transactions/investments/short/{id}/show", 'HomeController@showShortInvestments')->name('short-investment.show');
Route::get("/admin/transactions/investments/long/{id}/show", 'AdminController@showLongInvestments')->name('admin-long-investment.show');
//Route::get("/transactions/investments/long/{id}/show", 'AdminController@showLongInvestments')->name('long-investment.show.admin');
Route::get('/investments/payout/approveNow/{id}', 'AdminController@payMilestone');
Route::get("/transactions/investments/long/{id}/show", 'HomeController@showLongInvestments')->name('long-investment.show');

Route::group(['middleware' => 'assign.guard:web,/login'],function(){

    Route::get('/profile', 'HomeController@profile');
    Route::post('/profile', 'HomeController@editProfile')->name('profile.edit');
    Route::post('/profile/kin', 'HomeController@editProfileKin')->name('profile.edit.kin');
    Route::get('/banks', 'HomeController@banks');
    Route::get('/banks/delete/{id}', 'HomeController@deleteBank');
    Route::get('/banks/add', 'HomeController@addBank');
    Route::post('/banks/add', 'HomeController@addBankPost')->name('bank.add');
    Route::get('/bank/edit/{id}', 'HomeController@editBank');
    Route::post('/bank/edit', 'HomeController@editBankPost')->name('bank.update');

    // Route::group(['middleware' => 'hasPreferredBank'], function (){
        Route::get('/', 'HomeController@index');
        Route::get('/packages', 'HomeController@packages');
        Route::get('/farmlist', 'HomeController@farmlist');
        Route::get('/farmlist/long', 'FarmlistController@longTermFarm');
        Route::get('/farmlist/pending', 'HomeController@pending');
        Route::get('/farmlist/closed', 'HomeController@closed');
        Route::get('/farmlist/opened', 'HomeController@opened');
        Route::get('/farmlist/invest/{slug}', 'HomeController@invest')->name('farm.invest');
        Route::get('/farmlist/{slug}', 'HomeController@show')->name('farm.show');
        Route::get('/farmlist/long/{slug}/show', 'FarmlistController@showLong')->name('farm.show.long');
        Route::get('/farmlist/long/invest/{slug}', 'FarmlistController@investLong')->name('farm.invest.long');
        Route::post('/farmlist/long/invest', 'FarmlistController@investLongPost')->name('invest.long.add');
        Route::post('/farmlist/invest', 'HomeController@investPost')->name('invest.add');
        Route::get('/farmlist/{category}/farms', 'HomeController@farmlistByCategory');

        Route::get('/wallet', 'HomeController@wallet');
        Route::post('/wallet/payouts', 'HomeController@addPayoutPost')->name('payouts.add');
        Route::post('/wallet/deposits', 'HomeController@addDepositPost')->name('deposits.add');
        Route::get('/logout', 'HomeController@logout');
        Route::get('/verify-payment', 'PaymentController@verifyPay');
        Route::get('/payOnline', 'HomeController@payOnline');
        Route::get('/news', 'HomeController@news');
        Route::get('/news/{slug}', 'HomeController@viewNews');
        Route::get('/news/list/{slug}', 'HomeController@newslist');
        Route::get('/referral/guide', 'HomeController@referralGuide')->name('refer.example');

        Route::get('/transactions', 'TransactionsController@index');
        Route::get('/transactions/investments/{type}', 'TransactionsController@investments');
        Route::get('/transactions/payouts', 'TransactionsController@payouts');
        Route::get('/transactions/deposits', 'TransactionsController@deposits');
        Route::get('/transactions/referrals', 'TransactionsController@referral');
        Route::get("/transactions/{type}/save/download", 'TransactionsController@download')->name('download.user.transactions');
        Route::post('interaccount-transfer', 'TransactionsController@interAccountTransfer')->name('interaccount.transfer');

        Route::get('bookings', 'BookingController@index')->name('booking.index');
        Route::post('bookings', 'BookingController@store')->name('bookings.store');
        Route::get('bookings/{booking}/delete', 'BookingController@destroy')->name('bookings.delete');

        Route::get('/notifications', function(){
            $notifications = \auth()->user()->notifications()->paginate(50);
            return view('notifications.index', compact('notifications'));
        });
        Route::get('/notifications/{id}', function($id){
            DB::table('notifications')->where('id', $id)->update(['read_at'=>\Carbon\Carbon::now()]);
            $notification = DB::table('notifications')->where('id', $id)->first();
            return view('notifications.show', ['id'=>$id, 'notification'=>$notification]);
        });
        Route::get('/notifications/myaction/viewall', function(Request $request){
            $user = request()->user();
            $user->unreadNotifications->map(function($n) use($request) {
                $n->markAsRead();
            });
            return redirect()->back();
        });
    // });

});

//Auth::guard('admin')->loginUsingId(2);
//Auth::loginUsingId(13);
