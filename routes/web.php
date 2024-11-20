<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

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

Route::post('/refresh', function (Request $request) {
    $signature = 'sha256=' . hash_hmac('sha256', $request->getContent(), "GSSSYzBqcO6JvyH8kiI2Zsco0VmkuFwb8J0MVawQCbAehkNUvjsMwq6gaBDLuep");

    if (!hash_equals($request->header('x-hub-signature-256'), $signature)) {
        $message = "Signatures didn't match";
        return;
    }
    exec('git pull origin main ', $output);
    echo json_encode($output);
    return response(['message'=>"success"]);
});

Route::get('/force', function (Request $request) {
    exec('/usr/bin/git pull 2>&1', $output);
    echo json_encode($output);
    return response(['message'=>"success"]);
});

Route::get('/', function () {
    return redirect('/admin');
});


Route::get('/download', function () {
    $filePath =  ('source.zip');
    
    // Check if the file exists
    if (File::exists($filePath)) {
        // Set headers for file download
        $headers = [
            'Content-Type' => 'application/zip',
        ];

        // Return the file as a response
        return Response::download($filePath, 'source.zip', $headers);
    } else {
        // If file not found, return error response
        return Response::make('File not found.', 404);
    }
});
Route::get('/download-report/{file}', function ($file) {
    $filePath = public_path( $file);
    if (file_exists($filePath)) {
        return Response::download($filePath)->deleteFileAfterSend(true);
    } else {
        return abort(404, 'File not found');
    }
});