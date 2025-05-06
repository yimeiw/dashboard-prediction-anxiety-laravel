<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictAnxietyController extends Controller
{
    public function predictAnxiety(Request $request)
    {
        $data = [
            "age" => (int) $request->input("age"),
            "gender" => $request->input("gender"),
            "sleepHours" => (float) $request->input("sleepHours"),
            "physicalActivity" => (float) $request->input("physicalActivity"),
            "caffeineInTake" => (int) $request->input("caffeineInTake"),
            "workStresLevel" => (int) $request->input("workStresLevel"),
            "heartRate" => (int) $request->input("heartRate"),
            "heartRateVariability" => (int) $request->input("heartRateVariability"),
            "cortisolLevel" => (int) $request->input("cortisolLevel"),
            "therapySession" => (int) $request->input("therapySession"),
            "sleepQuality" => (int) $request->input("sleepQuality"),
            "overthinking" => (int) $request->input("overthinking"),
            "lackOfConfidence" => (int) $request->input("lackOfConfidence"),
            "lackOfSleep" => (int) $request->input("lackOfSleep"),
            "exerciseFrequency" => (int) $request->input("exerciseFrequency"),
            "loneliness" => (int) $request->input("loneliness"),
        ];
        $data['stressScore'] = $data['workStresLevel'] + $data['cortisolLevel'] + $data['lackOfSleep'];
        $data['sleepQuality_sleepHours'] = $data['sleepQuality'] * $data['sleepHours'];

        function normalize($value, $min, $max) {
            return round(10 * ($value - $min) / ($max - $min));
        }
        
        $normalizedData = [
            'sleepHours' => normalize($data['sleepHours'], 0, 12), // tidur 0–12 jam
            'physicalActivity' => normalize($data['physicalActivity'], 0, 10),
            'sleepQuality' => $data['sleepQuality'], // jika sudah 0–10
            'exerciseFrequency' => normalize($data['exerciseFrequency'], 0, 10),
            'overthinking' => $data['overthinking'],
            'lackOfConfidence' => $data['lackOfConfidence'],
            'lackOfSleep' => $data['lackOfSleep'],
            'loneliness' => $data['loneliness'],
            'workStresLevel' => $data['workStresLevel']
        ];

        $response = Http::post('http://localhost:8001/predict', $data);

        if ($response->successful()) {
            return view('prediction-result', [
                'inputData' => $data,
                'normalizedData' => $normalizedData,
                'suggestion' => $response->json()['suggestion'],
                'prediction' => $response->json()['prediction'],
                'top_features' => $response->json()['top_features'],
                'averageByGender' => [
                    'Female' => [
                        'sleepHours' => 7.2,
                        'physicalActivity' => 8.3,
                        'caffeineInTake' => 120,
                        'workStresLevel' => 3.9,
                        'stressLevel' => 4.1,
                        'heartRate' => 71,
                        'heartRateVariability' => 70,
                        'cortisolLevel' => 14.9,
                        'therapySession' => 0.2,
                        'sleepQuality' => 8.2,
                        'overthinking' => 4.0,
                        'lackOfConfidence' => 3.0,
                        'lackOfSleep' => 2.0,
                        'exerciseFrequency' => 4.0,
                        'loneliness' => 3.0
                    ],
                    'Male' => [
                        'sleepHours' => 7.0,
                        'physicalActivity' => 7.3,
                        'caffeineInTake' => 135,
                        'workStresLevel' => 4.2,
                        'stressLevel' => 4.0,
                        'heartRate' => 73,
                        'heartRateVariability' => 68,
                        'cortisolLevel' => 14.8,
                        'therapySession' => 0.3,
                        'sleepQuality' => 8.0,
                        'overthinking' => 4.2,
                        'lackOfConfidence' => 3.1,
                        'lackOfSleep' => 2.1,
                        'exerciseFrequency' => 4.2,
                        'loneliness' => 3.2
                    ],
                    'Other' => [
                        'sleepHours' => 7.1,
                        'physicalActivity' => 1.0,
                        'caffeineInTake' => 110,
                        'workStresLevel' => 3.6,
                        'stressLevel' => 4.3,
                        'heartRate' => 72,
                        'heartRateVariability' => 67,
                        'cortisolLevel' => 14.7,
                        'therapySession' => 0.3,
                        'sleepQuality' => 7.6,
                        'overthinking' => 4.4,
                        'lackOfConfidence' => 4.0,
                        'lackOfSleep' => 2.4,
                        'exerciseFrequency' => 2.7,
                        'loneliness' => 3.6
                    ]
                ]


            ]);
    
        } else {
            return response()->json([
                'error' => 'Failed to predict',
                'details' => $response->body()
            ], $response->status());
        }
    }

}
