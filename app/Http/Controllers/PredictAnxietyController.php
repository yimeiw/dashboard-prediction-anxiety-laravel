<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PredictAnxietyController extends Controller
{
    public function predict(Request $request)
    {
        // Ambil data dari form
        $inputData = $request->only([
            'age', 'gender', 'occupation', 'sleepHours', 'physicalActivity', 'caffeineInTake',
            'workStressLevel', 'stressLevel', 'heartRate', 'heartRateVariability',
            'cortisolLevel', 'therapySession', 'sleepQuality',
            'overthinking', 'lackOfConfidence', 'lackOfSleep', 'exerciseFrequency', 'loneliness'
        ]);

        // Kirim ke backend FastAPI
        try {
            $response = Http::post('http://127.0.0.1:8001/predict', $inputData);

            if ($response->successful()) {
                $prediction = $response->json()['prediction'];
                return view('prediction-result', ['prediction' => $prediction]);
            } else {
                return back()->withErrors(['msg' => 'Failed to get prediction from backend.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Error: ' . $e->getMessage()]);
        }
    }
}