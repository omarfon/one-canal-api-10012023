<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', ['middleware' => ['json.response']]], function () {
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
        Route::post('login', 'LoginController@authenticate');

        Route::post('validate', 'ValidateController@validateAccount');
        Route::post('validate/token', 'ValidateController@validateToken');
        Route::post('validate/password', 'ValidateController@validatePassword');

        Route::post('forgot-password', 'ForgotPasswordController@sendMail');
        Route::post('forgot-password/token', 'ForgotPasswordController@validateToken');
        Route::post('forgot-password/password', 'ForgotPasswordController@recoverPassword');
    });

    Route::group(['namespace' => 'General', 'prefix' => 'general'], function () {
        Route::post('formats/html', 'FormatController@getFormatHtml');
        Route::post('formats/pdf', 'FormatController@getFormatPdf');
    });

    Route::group(['middleware' => 'auth:sanctum', 'namespace' => 'Auth'], function () {
        Route::post('auth/logout', 'LoginController@logout');
    });

    Route::group(['namespace' => 'Auth', 'prefix' => 'admin/auth'], function () {
        Route::post('login', 'LoginController@adminAuthenticate');

        Route::post('forgot-password', 'ForgotPasswordController@sendAdminMail');
        Route::post('validate-code', 'ForgotPasswordController@validateCodeAdmin');
        Route::post('change-password', 'ForgotPasswordController@changePasswordAdmin');
    });

    Route::group(['namespace' => 'Api'], function () {
        Route::group(['prefix' => 'masters'], function () {
            Route::get('/document_types', 'MasterController@indexDocumentTypes');
            Route::get('/reasons', 'MasterController@indexReasons');
            Route::get('/terms', 'MasterController@indexTerms');
            Route::get('/banks', 'MasterController@indexBanks');
            Route::get('/marital-status', 'MasterController@indexMaritalStatus');
        });

        Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'masters'], function () {
            Route::get('/fees', 'MasterController@indexFees');
            Route::get('/salary-advance-format', 'MasterController@indexAdvanceFormat');
        });
    });

    Route::group(['middleware' => 'auth:sanctum', 'namespace' => 'Api'], function () {
        Route::post('logout', 'LoginController@logout');

        Route::group(['prefix' => 'me'], function () {
            Route::get('', 'MeController@get');
            Route::post('', 'MeController@updateProfile');
            Route::patch('/password', 'MeController@updatePassword');
            Route::get('/accounts', 'AccountController@index');
            Route::post('/accounts', 'AccountController@store');
            Route::patch('/toggle-salary-view', 'MeController@toggleSalaryView');
        });

        Route::group(['prefix' => 'salary-advances'], function () {
            Route::get('history', 'SalaryAdvanceController@indexHistory');
            Route::get('payment-period', 'SalaryAdvanceController@getPaymentPeriod');
            Route::post('', 'SalaryAdvanceController@store');
        });
    });

    Route::group(['middleware' => 'auth:sanctum', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
        Route::group(['prefix' => 'me'], function () {
            Route::get('', 'MeController@get');
            Route::post('/change-password', 'MeController@updatePassword');
        });

        Route::group(['prefix' => 'statistics'], function () {
            Route::get('/{chart}', 'StatisticsController@get');
        });

        Route::group(['prefix' => 'activities'], function () {
            Route::get('', 'ActivityController@me');
        });

        Route::group(['prefix' => 'administrators'], function () {
            Route::get('', 'AdministratorController@index');
            Route::post('', 'AdministratorController@store');
            Route::put('/{id}', 'AdministratorController@update');
            Route::delete('/{id}', 'AdministratorController@delete');

            Route::post('/selected/delete', 'AdministratorController@deleteSelected');
            Route::post('/selected/change-status', 'AdministratorController@changeStatusSelected');
        });

        Route::group(['prefix' => 'banks'], function () {
            Route::get('/all', 'BankController@all');
            Route::get('', 'BankController@index');
            Route::post('', 'BankController@store');
            Route::put('/{id}', 'BankController@update');
            Route::delete('/{id}', 'BankController@delete');

            Route::post('/selected/delete', 'BankController@deleteSelected');
            Route::post('/selected/change-status', 'BankController@changeStatusSelected');
        });

        Route::group(['prefix' => 'business'], function () {
            Route::get('/all', 'BusinessController@indexAll');
            Route::get('', 'BusinessController@index');
            Route::post('', 'BusinessController@store');
            Route::put('/{id}', 'BusinessController@update');
            Route::delete('/{id}', 'BusinessController@delete');

            Route::post('/selected/delete', 'BusinessController@deleteSelected');
            Route::post('/selected/change-status', 'BusinessController@changeStatusSelected');

            Route::get('/{buisness_id}/fees-ranges', 'BusinessFeesRangeController@index');
            Route::post('/{business_id}/fees-ranges', 'BusinessFeesRangeController@store');
            Route::put('/{business_id}/fees-ranges/{fees_range_id}', 'BusinessFeesRangeController@update');
            Route::delete('/{business_id}/fees-ranges/{fees_range_id}', 'BusinessFeesRangeController@delete');
        });

        Route::group(['prefix' => 'clients'], function () {
            Route::post('/import', 'ClientController@import');
            Route::get('/download-template', 'ClientController@templateDownload');

            Route::get('', 'ClientController@index');
            Route::get('/{id}', 'ClientController@show');
            Route::post('', 'ClientController@store');
            Route::put('/{id}', 'ClientController@update');
            Route::delete('/{id}', 'ClientController@delete');

            Route::get('/document-types/all', 'ClientController@indexDocumentTypes');

            Route::post('/selected/delete', 'ClientController@deleteSelected');
            Route::post('/selected/change-status', 'ClientController@changeStatusSelected');
        });

        Route::group(['prefix' => 'client-accounts'], function () {
            Route::get('/{client_id}', 'ClientAccountController@index');
            Route::post('', 'ClientAccountController@store');
            Route::put('/{id}', 'ClientAccountController@update');
            Route::patch('/{id}', 'ClientAccountController@confirm');
            Route::delete('/{id}', 'ClientAccountController@delete');

            Route::post('/selected/delete', 'ClientAccountController@deleteSelectedAccounts');
            Route::post('/selected/change-status', 'ClientAccountController@changeStatusSelectedAccounts');
        });

        Route::group(['prefix' => 'client-history'], function () {
            Route::get('/{client_id}', 'ClientHistoryController@index');
            Route::put('/{id}', 'ClientHistoryController@update');

            Route::post('/selected/change-status', 'ClientHistoryController@changeStatusSelectedAccounts');
        });

        Route::group(['prefix' => 'reasons'], function () {
            Route::get('/all', 'ReasonController@all');
            Route::get('', 'ReasonController@index');
            Route::post('', 'ReasonController@store');
            Route::put('/{id}', 'ReasonController@update');
            Route::delete('/{id}', 'ReasonController@delete');

            Route::post('/selected/delete', 'ReasonController@deleteSelected');
            Route::post('/selected/change-status', 'ReasonController@changeStatusSelected');
        });

        Route::group(['prefix' => 'fees'], function () {
            Route::get('', 'FeeController@index');
            Route::put('/{id}', 'FeeController@update');
        });

        Route::group(['prefix' => 'salary-advances'], function () {
            Route::get('', 'SalaryAdvanceController@index');
            Route::get('/export', 'SalaryAdvanceController@export');
            Route::put('/{id}', 'SalaryAdvanceController@update');

            Route::post('/selected/change-status', 'SalaryAdvanceController@changeStatusSelected');

        });

        Route::group(['prefix' => 'imports'], function () {
            Route::get('', 'ImportController@index');
            Route::get('/file/{filename}', 'ImportController@file');
        });

        Route::group(['prefix' => 'configurations'], function () {
            Route::get('', 'ConfigurationController@get');
            Route::put('/{id}', 'ConfigurationController@update');
        });

        Route::group(['prefix' => 'documents'], function () {
            Route::get('/{type}', 'DocumentController@get');
            Route::put('/{type}', 'DocumentController@update');
        });
    });
});

Route::group(['prefix' => 'v2', ['middleware' => ['json.response']]], function () {
    Route::group(['namespace' => 'Auth\v2', 'prefix' => 'auth'], function () {
        Route::post('validate', 'ValidateController@validateAccount');
        Route::post('validate/token', 'ValidateController@validateToken');
        Route::post('validate/password', 'ValidateController@validatePassword');

        Route::post('forgot-password', 'ForgotPasswordController@sendMail');
        Route::post('forgot-password/token', 'ForgotPasswordController@validateToken');
        Route::post('forgot-password/password', 'ForgotPasswordController@recoverPassword');
    });
});
