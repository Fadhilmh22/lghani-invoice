<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\AirlinesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HotelVoucherController;
use App\Http\Controllers\HotelInvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TopUpController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/actionlogin', [LoginController::class, 'actionlogin'])->name('actionlogin');

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::get('/actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('register', [RegisterController::class, 'register'])->name('register');
Route::post('register/action', [RegisterController::class, 'actionregister'])->name('actionregister')->middleware('auth');


    Route::group(['prefix' => 'airline'], function() {
        Route::get('/', [AirlinesController::class, 'index'])->name('index');
        Route::get('/new', [AirlinesController::class, 'create'])->name('create');
        Route::post('/', [AirlinesController::class, 'save'])->name('save');
        Route::get('/{id}', [AirlinesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AirlinesController::class, 'update'])->name('update');
        Route::delete('/{id}', [AirlinesController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => 'airports'], function() {
        Route::get('/', [AirportController::class, 'index'])->name('airports.index');
        Route::get('/new', [AirportController::class, 'create'])->name('airports.create');
        Route::post('/', [AirportController::class, 'store'])->name('airports.store');
        Route::get('/{id}/edit', [AirportController::class, 'edit'])->name('airports.edit');
        Route::put('/{id}', [AirportController::class, 'update'])->name('airports.update');
        Route::delete('/{id}', [AirportController::class, 'destroy'])->name('airports.destroy');
    });

    Route::group(['prefix' => 'customer'], function() {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/new', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'save'])->name('save');
        Route::get('/{id}', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::get('/search', [CustomerController::class, 'search'])->name('customer.search');
        Route::get('/initial_data', [CustomerController::class, 'initialData'])->name('customer.initial_data');
    });

    Route::group(['prefix' => 'invoice'], function() {
        Route::get('/', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::get('/new', [InvoiceController::class, 'create'])->name('invoice.create');
        Route::post('/', [InvoiceController::class, 'save'])->name('invoice.store');
        Route::get('/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('invoice.update');
        Route::delete('/{id}', [InvoiceController::class, 'deleteProduct'])->name('invoice.delete_product');
        Route::delete('/{id}/delete', [InvoiceController::class, 'destroy'])->name('invoice.destroy');        
        Route::get('/{id}/print', [InvoiceController::class, 'generateInvoice'])->name('invoice.print');
        Route::get('/{id}/printdisc', [InvoiceController::class, 'generateInvoicedisc'])->name('invoice.printdisc');
        Route::post('/ubah-status/{id}', [InvoiceController::class, 'ubahStatus'])->name('invoice.ubah-status');
        Route::post('/getInvoiceDetail', [InvoiceController::class, 'getInvoiceDetail'])->name('invoice.invoice_detail');
    });

    Route::group(['prefix' => 'ticket'], function() {
        Route::get('/', [TicketController::class, 'index'])->name('ticket.index');
        Route::get('/create', [TicketController::class, 'create'])->name('ticket.create'); 
        Route::post('/', [TicketController::class, 'store'])->name('ticket.store');
        Route::get('/{id}/edit', [TicketController::class, 'edit'])->name('ticket.edit');
        Route::get('/{id}/print', [TicketController::class, 'print'])->name('ticket.print');
        Route::put('/{id}', [TicketController::class, 'update'])->name('ticket.update');
        Route::delete('/{id}', [TicketController::class, 'destroy'])->name('ticket.destroy');
        Route::get('/{id}/passengers', [TicketController::class, 'getPassengers']); // Untuk ambil list nama di modal
        Route::get('/print-split/{ticket_id}/{passenger_id}', [TicketController::class, 'printSplit'])->name('ticket.print_split');
        Route::post('/tickets/bulk-invoice', [TicketController::class, 'bulkInvoice'])->name('ticket.bulkInvoice');
        Route::post('/ticket/auto-parse', [TicketController::class, 'autoParse'])->name('ticket.autoParse');
    });

    // new top up airline routes (also allow Owner role)
    // topup routes moved outside main admin group below

    Route::group(['prefix' => 'report'], function() {
        Route::get('/', [ReportController::class, 'index'])->name('report.index');
        Route::get('/print', [ReportController::class, 'generateReport'])->name('report.print');
        Route::get('/hotel', [ReportController::class, 'hotel'])->name('report.hotel');
        Route::get('/printhotel', [ReportController::class, 'generateHotelReport'])->name('report.printhotel');
        Route::get('/piutang', [ReportController::class, 'piutang'])->name('report.piutang');
        Route::get('/printpiutang', [ReportController::class, 'generatePiutang'])->name('report.printpiutang');
    });

    Route::group(['prefix' => 'passenger'], function() {
        Route::get('/', [PassengerController::class, 'index'])->name('index');
        Route::get('/new', [PassengerController::class, 'create'])->name('create');
        Route::post('/', [PassengerController::class, 'save'])->name('save');
        Route::get('/{id}', [PassengerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PassengerController::class, 'update'])->name('update');
        Route::delete('/{id}', [PassengerController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => 'hotel'], function() {
        Route::get('/', [HotelController::class, 'index'])->name('index');
        Route::get('/new', [HotelController::class, 'create'])->name('create');
        Route::post('/', [HotelController::class, 'save'])->name('save');
        Route::get('/{id}', [HotelController::class, 'edit'])->name('edit');
        Route::put('/{id}', [HotelController::class, 'update'])->name('update');
        Route::delete('/{id}', [HotelController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => 'room'], function() {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/new', [RoomController::class, 'create'])->name('create');
        Route::post('/', [RoomController::class, 'save'])->name('save');
        Route::get('/{id}', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomController::class, 'destroy'])->name('destroy');

        Route::post('/getRoomByHotel', [RoomController::class, 'getRoomByHotel'])->name('room.getbyhotel');
        Route::post('/detail', [RoomController::class, 'detail'])->name('room.detail');
    });

    Route::group(['prefix' => 'hotel-voucher'], function() {
        Route::get('/', [HotelVoucherController::class, 'index'])->name('hotelvoucher.index');
        Route::get('/new', [HotelVoucherController::class, 'create'])->name('hotelvoucher.create');
        Route::post('/', [HotelVoucherController::class, 'save'])->name('hotelvoucher.save');
        Route::get('/{id}', [HotelVoucherController::class, 'edit'])->name('hotelvoucher.edit');
        Route::put('/{id}', [HotelVoucherController::class, 'update'])->name('hotelvoucher.update');
        Route::get('/room/{id}', [HotelVoucherController::class, 'room'])->name('hotelvoucher.room');
        Route::put('/room/{id}', [HotelVoucherController::class, 'updateRoom'])->name('hotelvoucher.updateRoom');
        Route::delete('/{id}', [HotelVoucherController::class, 'deleteProduct'])->name('hotelvoucher.delete_product');
        Route::delete('/{id}/delete', [HotelVoucherController::class, 'destroy'])->name('hotelvoucher.destroy');
        Route::get('/{id}/print', [HotelVoucherController::class, 'generateVoucher'])->name('hotelvoucher.print');
        Route::get('/invoice/{id}/print', [HotelVoucherController::class, 'generateVoucherByInvoice'])->name('hotelvoucherbyinvoice.print');

        Route::post('/getRoomDetail', [HotelVoucherController::class, 'getRoomDetail'])->name('hotelvoucher.room_detail');
    });

    Route::group(['prefix' => 'hotel-invoice'], function() {
        Route::get('/', [HotelInvoiceController::class, 'index'])->name('hotelinvoice.index');
        Route::get('/new', [HotelInvoiceController::class, 'create'])->name('hotelinvoice.create');
        Route::post('/', [HotelInvoiceController::class, 'save'])->name('hotelinvoice.save');
        Route::get('/{id}', [HotelInvoiceController::class, 'edit'])->name('hotelinvoice.edit');
        Route::put('/{id}', [HotelInvoiceController::class, 'update'])->name('hotelinvoice.update');
        Route::get('/room/{id}', [HotelInvoiceController::class, 'room'])->name('hotelinvoice.room');
        Route::put('/room/{id}', [HotelInvoiceController::class, 'updateRoom'])->name('hotelinvoice.updateRoom');
        Route::delete('/{id}', [HotelInvoiceController::class, 'deleteProduct'])->name('hotelinvoice.delete_product');
        Route::delete('/{id}/delete', [HotelInvoiceController::class, 'destroy'])->name('hotelinvoice.destroy');
        Route::post('/{id}/ubah-statusinv', [HotelInvoiceController::class, 'ubahStatus'])->name('hotelinvoice.ubah-status');
        Route::get('/{id}/print', [HotelInvoiceController::class, 'generateInvoice'])->name('hotelinvoice.print');
        Route::get('/{id}/printdisc', [HotelInvoiceController::class, 'generateInvoicedisc'])->name('hotelinvoice.printdisc');

        Route::post('/getRoomDetail', [HotelInvoiceController::class, 'getRoomDetail'])->name('hotelinvoice.room_detail');
    });
    
    

// standalone topup routes accessible by Admin, Staff, or Owner
Route::group(['middleware'=>['auth','AdminMiddleware:Admin,Staff,Owner'],'prefix'=>'topup'], function() {
    Route::get('/', [TopUpController::class, 'index'])->name('topup.index');
    Route::post('/', [TopUpController::class, 'store'])->name('topup.store');
});
