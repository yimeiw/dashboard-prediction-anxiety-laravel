<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    // public function index()
    // {
    //     if (!session()->has('normalizedData')) {
    //         return redirect()->route('anxiety.form')->with('error', 'Data belum tersedia. Silakan isi form prediksi terlebih dahulu.');
    //     }
    //     return view('dashboard', [
    //         'inputData' => session('inputData'),
    //         'normalizedData' => session('normalizedData'),
    //         'prediction' => session('prediction'),
    //         'top_features' => session('top_features'),
    //         'averageByGender' => session('averageByGender'),
    //     ]);
    // }
}
