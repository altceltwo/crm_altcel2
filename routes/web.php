<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('activations', 'ActivationController')->middleware('auth');
Route::resource('offers','OfferController')->middleware('auth');
Route::resource('rates','RateController')->middleware('auth');
Route::resource('schedules','ScheduleController')->middleware('auth');
Route::resource('clients','ClientController')->middleware('auth');
Route::resource('facturacion', 'InvoiceController')->middleware('auth');
Route::resource('assignment', 'AssignmentController')->middleware('auth');
Route::resource('dealer', 'DealerController')->middleware('auth');

// Petitions
// Route::post('/petition');

//Devices
Route::resource('devices', 'DeviceController')->middleware('auth');

// OpenPay Routes
Route::post('/charge', 'OpenPayController@store');
Route::post('/create-reference-openpay','ReferenceController@createReference')->name('create-reference.post');
Route::post('/create-reference-api','ReferenceController@createReference');
Route::post('/webhook-openpay','WebhookController@notificationOpenPay');
Route::get('/webhook-openpay','WebhookController@openpayPays')->name('webhook-openpay.get')->middleware('auth');
Route::get('/webhook-pendings','PaymentController@paymentsPendings')->name('webhook-payments-pending.get')->middleware('auth');
Route::get('/webhook-overdue','PaymentController@paymentsOverdue')->name('webhook-payments-overdue.get')->middleware('auth');

// Activations Routes
Route::get('/activationsGeneral', 'ActivationController@activationGeneral')->name('activation-general.post');
Route::post('/activationsGeneralApi', 'ActivationController@activationGeneral');
Route::get('/activationsEthernet', 'ActivationController@activationEthernet')->name('activation-ethernet.post');
Route::get('/preactivations', 'ActivationController@preactivationsIndex')->name('preactivations.index')->middleware('auth');
Route::post('/change-rate-preactivate','ActivationController@changeRatePreactivate')->name('changeRatePreactivate')->middleware('auth');
Route::delete('/rollback-preactivate/{activation}','ActivationController@rollbackPreactivate')->name('rollbackPreactivate')->middleware('auth');
Route::delete('/rollback-preactivate-api/{activation}','ActivationController@rollbackPreactivate');
Route::post('/execute-activation/{activation}','ActivationController@executeActivation')->name('executeActivation')->middleware('auth');

// Administrator Routes
Route::get('/users', 'UserController@showUsers')->name('show-users.get')->middleware('auth');
Route::post('/users', 'UserController@addUser')->name('add-user.post')->middleware('auth');
Route::get('/get-user', 'UserController@getUser')->name('get-user.get')->middleware('auth');
Route::post('/update-user', 'UserController@updateUser')->name('update-user.post')->middleware('auth');
Route::post('/change-rol-user', 'UserController@changeRolUser')->name('change-rol.post');
Route::get('/promoters', 'AssignmentController@index')->name('promoters.get');
Route::get('/assignment/show-promoter/{promoter}','AssignmentController@showAssignments')->name('showAssignments')->middleware('auth');

// Client Routes
Route::get('/rechargeGenerate', 'ClientController@rechargeGenerateClient')->name('recharge-view-client.get')->middleware('auth');
Route::get('/clients-pay-all', 'ClientController@clientsPayAll')->name('clients-pay-all.get')->middleware('auth');
Route::get('/clients-details/{id}', 'ClientController@clientDetails')->middleware('auth');;
Route::post('/buying', 'CheckoutController@buying');
Route::get('/search/clients', 'ClientController@searchClients')->name('search-clients.get');
Route::get('/search/client-product', 'ClientController@searchClientProduct')->name('search-client-product.get');
Route::get('/generateReference/{id}/{type}/{user_id}','ClientController@generateReference')->middleware('auth');
Route::get('/show-reference','ClientController@showReferenceClient');
Route::get('/show-product-details/{id_dn}/{id_act}/{service}','ClientController@productDetails');
Route::get('/add-client-async','ClientController@storeAsync')->name('addClientAsync.get');
Route::get('/get-number-instalation','ClientController@getNumberInstalation')->name('getNumberInstalation.get');
Route::get('/set-number-instalation','ClientController@setNumberInstalation')->name('setNumberInstalation.get');
Route::get('/prospects','ClientController@prospects')->name('prospects.index')->middleware('auth');
Route::post('/get-all-data-client','ClientController@getAllDataClient')->name('getAllDataClient.post')->middleware('auth');
Route::post('/set-all-data-client','ClientController@setAllDataClient')->name('setAllDataClient.post')->middleware('auth');
Route::get('/special-operations','ClientController@specialOperations')->name('operations.specials')->middleware('auth');
Route::get('/get-data-client-change-product','ClientController@getDataClientChangeProduct')->name('getDataClientChangeProduct.get');
Route::get('/get-data-client-by-sim','ClientController@getDataClientBySIM')->name('getDataClientBySIM.get');

// Offers Routes
Route::get('/get-offer','OfferController@findOffer')->name('get-offer.get')->middleware('auth');
Route::get('/getAllOffers','OfferController@getAllOffer')->name('getAllOffers.get');
Route::get('/getAllOffersByType','OfferController@getAllOfferByType')->name('getAllOffersByType.get');


// Rates Numbers(DN's)
Route::post('/getDn', 'NumberController@getNumber')->name('search-dn.post');
Route::post('/activate-deactivate/DN', 'AltanController@activateDeactivateDN')->name('activate-deactivate.post');
Route::get('/activate-deactivate/DN-finance', 'AltanController@activateDeactivateDN');
Route::post('/activate-deactivate/DN-api', 'AltanController@activateDeactivateDN');
Route::get('/consultUF','AltanController@consultUF')->name('consultUF.get');
Route::post('/predeactivate-reactivate','AltanController@predeactivateReactivate')->name('predeactivate.reactivate');
Route::get('/get-offers-rates-diff','RateController@getOffersRatesDiff')->name('getOffersRatesDiff.get');
Route::get('/get-offers-rates-diff-api','RateController@getOffersRatesDiffAPI')->name('getOffersRatesDiffAPI.get');
Route::get('/get-offers-rates-diff-api-public','RateController@getOffersRatesDiffAPIPublic')->name('getOffersRatesDiffAPIPublic.get');
Route::get('/get-offers-rates-surplus','RateController@getOffersRatesSurplus')->name('getOffersSurplus.get');
Route::post('/change-product','AltanController@changeProduct')->name('changeProduct.post');
Route::get('/purchase','AltanController@productPurchase')->name('purchase');
Route::post('/purchase-api','AltanController@productPurchase')->name('purchase-api');
Route::post('/locked', 'AltanController@locked')->name('locked');
Route::get('/status', 'AltanController@statusImei')->name('status');
Route::get('/consultUFSpecial/{msisdn}','ClientController@getInfoUF')->name('consultUFSpecial.get')->middleware('auth');

// Rates Routes
Route::post('/get-rates', 'RateController@getRates')->name('get-rates.post');
Route::post('/get-rates-alta', 'RateController@getRatesAlta')->name('get-rates-alta.post');
Route::post('/get-rates-alta-api', 'RateController@getRatesAltaApi');
Route::post('/get-politics-rates','RateController@getPoliticsRates')->name('get-politics-rates.post');

// Ethernet Admin
Route::get('/ethernetAdmin','AdminController@index')->name('ethernet-admin.get')->middleware('auth');

// Packs Routes
Route::post('/create-pack','AdminController@createPack');
Route::post('/create-radiobase','AdminController@createRadiobase');
Route::get('/get-pack-ethernet','AdminController@getPackEthernet')->name('get-pack-ethernet.get');
Route::post('/update-pack-ethernet/{pack_id}','AdminController@updatePackEthernet')->name('update-pack-ethernet.get');

// Politics Routes
Route::get('/create-politic','AdminController@createPoliticRate')->name('politicRate.create')->middleware('auth');
Route::post('/create-politic','AdminController@insertPoliticRate')->name('politicRate.create');
Route::get('/politics/delete/{politic_id}','AdminController@destroy')->name('politic.delete');
Route::get('/politics/edit/{politic_id}','AdminController@getPolitic')->name('politic.edit');
Route::put('/politics/update/{politic}','AdminController@updatePolitic')->name('politic.update');

// Pays Routes
Route::get('/save-manual-pay','WebhookController@saveManualPay')->name('save-manual-pay.get');
Route::get('/get-data-payment','ActivationController@getDataPayment')->name('getDataPayment.get');
Route::get('/get-data-monthly','ActivationController@getDataMonthly')->name('getDataMonthly.get');
Route::get('/set-data-monthly','ActivationController@setDataMonthly')->name('setDataMonthly.get');

Route::post('/notifications-webhook', 'WebhookController@notificationWHk');
Route::post('/conekta-webhook', 'WebhookController@notificationWHkConekta');
Route::get('/date-pay', 'ActivationController@datePay')->name('date-pay');
Route::get('/my-profile','UserController@myProfile')->name('myProfile');
Route::get('/update-my-profile','UserController@updateMyProfile')->name('update-my-profile');
Route::get('/change-status-packsNrates','AdminController@changeStatusPacksRates')->name('change-status.rates-packs');
Route::post('/overdue-payments','AdminController@checkOverduePayments');
Route::get('/set-payment-status','ActivationController@setPaymentStatus')->name('setPaymentStatus.get');
Route::get('/update-price-device', 'DeviceController@updatePriceDevice')->name('updatePriceDevice.get');

// Card Payments
Route::get('/card-payment','OpenPayController@cardPayment');
Route::post('/send-card-payment','OpenPayController@cardPaymentSend');

// Done by Charles
Route::get('/get-imei','DeviceController@getImei')->name('getImei.get');
Route::post('/create-payments','PaymentController@createPaymentsSwitchCase');
Route::post('/create-payments-ethernet','PaymentController@createPaymentsEthernet');
Route::post('/check-payments-overdue','PaymentController@checkOverduePaymentsSwitchCase');
Route::post('/create-payments-sandbox','PaymentController@createPaymentsSandbox');

    // Exports
Route::get('/new-clients-export-excel','ClientController@exportNewClients')->name('newClients.excel');
Route::get('/rates-actives-export-excel','RateController@exportRatesActives')->name('rates.excel');

//Invoice
Route::resource('invoice', 'InvoiceController@invoice');
Route::get('/invoice/{facturacion}', 'InvoiceController@destroy');
Route::get('/details/{facturacion}','InvoiceController@invoice');
Route::get('invoices', 'InvoiceController@invoices');
Route::post('/invoice-join', 'InvoiceController@invoiceJoin')->name('invoiceJoin.post');

//change
Route::get('change', 'AltanController@changeLink')->name('changeLink');
Route::patch('/updateCoordinate', 'AltanController@updateCoordinate')->name('updateCoordinate');

//serviciabilidad
Route::get('serviciabilidad','AltanController@serviciabilidad')->name('serv');

        
// Assignments
// Route::post('/assignment','');

Route::get('change-product', 'PaymentController@changeProductPayment')->name('changeProduct');
Route::get('excedentes', 'PaymentController@excedentes')->name('excedentes');

//admin
Route::get('generalConcesiones','AdminController@indexConcesiones')->name('indexConcesiones');
Route::get('/cortes/{id}', 'AdminController@consulta')->name('cortes.get');
Route::post('consultaCortes', 'AdminController@consultaCortes')->name('consulta.post');
Route::post('updateStatusCortes', 'AdminController@statusCortes')->name('status.update');
Route::post('payAll','AdminController@payAll')->name('payAll');

// Petitions
Route::get('solicitudes', 'PetitionController@index')->name('solicitudes');
Route::get('completadas','PetitionController@show')->name('completadas');
Route::get('activationOperaciones', 'PetitionController@activationOperaciones')->name('activation.get');
Route::get('completadasFinanzas','PetitionController@recibidosFinance')->name('recibidos');
Route::get('completadas','PetitionController@show')->name('completadas')->middleware('auth');
Route::get('activationOperaciones', 'PetitionController@activationOperaciones')->name('activation.get');
Route::post('collect-money', 'PetitionController@collectMoney')->name('collectMoney');
Route::post('save-collected', 'PetitionController@saveCollected')->name('saveCollected');
