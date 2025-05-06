<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Prediction Result') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    Prediksi Tingkat Kecemasan:
                    <span id="predictionLabel" class="font-bold text-blue-600"></span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Chart Card -->
                    <div class="bg-gray-50 p-4 rounded-xl shadow-md">
                        <h4 class="text-md font-semibold mb-2">Comparison Chart</h4>
                        <canvas id="comparisonChart" class="w-full h-64"></canvas>
                        <p class="text-sm text-gray-600 mt-2">
                            Perbandingan nilai atribut Anda dengan rata-rata pengguna dengan gender yang sama.
                            Membantu melihat apakah Anda cenderung lebih tinggi atau rendah dalam faktor-faktor tertentu.
                        </p>
                    </div>

                    <!-- Line Chart -->
                    <div class="bg-gray-50 p-4 rounded-xl shadow-md">
                        <h4 class="text-md font-semibold mb-2">Line Chart - Stress Breakdown</h4>
                        <canvas id="stressLineChart" class="w-full h-64"></canvas>
                        <p class="text-sm text-gray-600 mt-2">
                            Menampilkan tiga kontributor utama stres Anda: tingkat stres kerja, kadar kortisol, dan kurang tidur.
                        </p>
                    </div>

                    <!-- Radar Comparison -->
                    <div class="bg-gray-50 p-4 rounded-xl shadow-md">
                        <h4 class="text-md font-semibold mb-2">Radar Chart - Comparison</h4>
                        <canvas id="radarChartComparison" class="w-full h-64"></canvas>
                        <p class="text-sm text-gray-600 mt-2">
                            Visualisasi bentuk profil Anda dibandingkan dengan rerata gender Anda untuk semua atribut.
                            Area lebih luas menunjukkan skor lebih tinggi.
                        </p>
                    </div>

                    
                    <!-- Normalized Radar -->
                    <div class="bg-gray-50 p-4 rounded-xl shadow-md">
                        <h4 class="text-md font-semibold mb-2">Radar Chart - Normalized Profile</h4>
                        <canvas id="radarChart" class="w-full h-64"></canvas>
                        <p class="text-sm text-gray-600 mt-2">
                            Menunjukkan profil gaya hidup dan kesehatan mental Anda dalam skala 0–10.
                            Data telah dinormalisasi untuk perbandingan yang adil antar faktor.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- CHART SCRIPT --}}
    <script>
        // Data parsing
        const inputData = @json($inputData);
        const normalizedData = @json($normalizedData);
        const prediction = @json($prediction);
        const topFeatures = @json($top_features); 
        const averageByGender = @json($averageByGender);
        const userGender = inputData.gender;
        const { gender, ...numericUserData } = inputData;
        const genderAverage = averageByGender[userGender];

        // Show prediction result
        const predictionLabel = document.getElementById("predictionLabel");
        predictionLabel.innerText = prediction;

        if (prediction === "High") {
            predictionLabel.classList.add("text-red-400");
        } else if (prediction === "Medium") {
            predictionLabel.classList.add("text-yellow-400");
        } else if (prediction === "Low") {
            predictionLabel.classList.add("text-green-400");
        }
        // Comparison Chart
        const compLabels = Object.keys(genderAverage);
        const userBarValues = compLabels.map(k => numericUserData[k]);
        const genderValues = compLabels.map(k => genderAverage[k]);

        new Chart(document.getElementById('comparisonChart'), {
            type: 'bar',
            data: {
                labels: compLabels,
                datasets: [
                    {
                        label: 'User',
                        data: userBarValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    },
                    {
                        label: `${userGender} Average`,
                        data: genderValues,
                        backgroundColor: 'rgba(255, 159, 64, 0.6)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'User vs Same-Gender Average'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Generate insight for comparison
        let compInsight = '';
        let maxDiff = 0;
        let maxLabel = '';
        for (let i = 0; i < compLabels.length; i++) {
            const diff = Math.abs(userBarValues[i] - genderValues[i]);
            if (diff > maxDiff) {
                maxDiff = diff;
                maxLabel = compLabels[i];
            }
        }
        compInsight = `Atribut dengan perbedaan paling besar dibanding rata-rata gender Anda adalah "${maxLabel}".`;

        // Tampilkan insight
        const compInsightElement = document.createElement('p');
        compInsightElement.className = "text-sm text-blue-700 mt-2";
        compInsightElement.innerText = compInsight;
        document.getElementById('comparisonChart').after(compInsightElement);



        // Radar Chart - Comparison
        const radarLabels = Object.keys(numericUserData);
        const userRadarValues = radarLabels.map(key => numericUserData[key]);
        const genderAvgValues = radarLabels.map(key => genderAverage[key]);

        new Chart(document.getElementById("radarChartComparison"), {
            type: 'radar',
            data: {
                labels: radarLabels,
                datasets: [
                    {
                        label: "User",
                        data: userRadarValues,
                        fill: true,
                        backgroundColor: "rgba(54, 162, 235, 0.2)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        pointBackgroundColor: "rgba(54, 162, 235, 1)"
                    },
                    {
                        label: `${userGender} Average`,
                        data: genderAvgValues,
                        fill: true,
                        backgroundColor: "rgba(255, 159, 64, 0.2)",
                        borderColor: "rgba(255, 159, 64, 1)",
                        pointBackgroundColor: "rgba(255, 159, 64, 1)"
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: `Radar Comparison: User vs ${userGender} Average`
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Generate radar comparison insight
        let radarMaxDiff = 0;
        let radarDiffLabel = '';
        for (let i = 0; i < radarLabels.length; i++) {
            const diff = Math.abs(userRadarValues[i] - genderAvgValues[i]);
            if (diff > radarMaxDiff) {
                radarMaxDiff = diff;
                radarDiffLabel = radarLabels[i];
            }
        }
        const radarInsightText = `Anda paling berbeda dengan rata-rata gender Anda pada atribut "${radarDiffLabel}".`;

        const radarInsightElement = document.createElement('p');
        radarInsightElement.className = "text-sm text-blue-700 mt-2";
        radarInsightElement.innerText = radarInsightText;
        document.getElementById('radarChartComparison').after(radarInsightElement);


        // Line Chart - Stress
        const stressLabels = ['Work Stress', 'Cortisol', 'Lack of Sleep'];
        const stressData = [
            inputData.workStresLevel,
            inputData.cortisolLevel,
            inputData.lackOfSleep,
        ];

        new Chart(document.getElementById("stressLineChart"), {
            type: 'line',
            data: {
                labels: stressLabels,
                datasets: [{
                    label: "Stress Contributors",
                    data: stressData,
                    borderColor: "rgba(239, 68, 68, 1)",
                    backgroundColor: "rgba(239, 68, 68, 0.2)",
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Stress Score Breakdown'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Generate insight for stress
        const maxStress = Math.max(...stressData);
        const maxStressLabel = stressLabels[stressData.indexOf(maxStress)];
        const stressInsight = `Kontributor stres tertinggi Anda adalah "${maxStressLabel}".`;

        const stressInsightElement = document.createElement('p');
        stressInsightElement.className = "text-sm text-red-700 mt-2";
        stressInsightElement.innerText = stressInsight;
        document.getElementById('stressLineChart').after(stressInsightElement);


        // Radar Chart - Normalized Profile
        const ctx = document.getElementById('radarChart').getContext('2d');

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: [
                    'Sleep Hours', 'Physical Activity', 'Sleep Quality', 'Exercise Frequency',
                    'Overthinking', 'Lack of Confidence', 'Lack of Sleep', 'Loneliness', 'Work Stress'
                ],
                datasets: [{
                    label: 'Your Profile',
                    data: [
                        {{ $normalizedData['sleepHours'] }},
                        {{ $normalizedData['physicalActivity'] }},
                        {{ $normalizedData['sleepQuality'] }},
                        {{ $normalizedData['exerciseFrequency'] }},
                        {{ $normalizedData['overthinking'] }},
                        {{ $normalizedData['lackOfConfidence'] }},
                        {{ $normalizedData['lackOfSleep'] }},
                        {{ $normalizedData['loneliness'] }},
                        {{ $normalizedData['workStresLevel'] }}
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        min: 0,
                        max: 10
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Lifestyle & Mental Health Profile'
                    }
                }
            }
        });

        // Generate insight for normalized radar
        const normLabels = [
            'Sleep Hours', 'Physical Activity', 'Sleep Quality', 'Exercise Frequency',
            'Overthinking', 'Lack of Confidence', 'Lack of Sleep', 'Loneliness', 'Work Stress'
        ];

        const normValues = [
            {{ $normalizedData['sleepHours'] }},
            {{ $normalizedData['physicalActivity'] }},
            {{ $normalizedData['sleepQuality'] }},
            {{ $normalizedData['exerciseFrequency'] }},
            {{ $normalizedData['overthinking'] }},
            {{ $normalizedData['lackOfConfidence'] }},
            {{ $normalizedData['lackOfSleep'] }},
            {{ $normalizedData['loneliness'] }},
            {{ $normalizedData['workStresLevel'] }}
        ];

        let highest = Math.max(...normValues);
        let lowest = Math.min(...normValues);
        let highLabel = normLabels[normValues.indexOf(highest)];
        let lowLabel = normLabels[normValues.indexOf(lowest)];

        const normInsight = `Faktor terkuat dalam profil Anda: "${highLabel}", dan terlemah: "${lowLabel}".`;

        const normInsightElement = document.createElement('p');
        normInsightElement.className = "text-sm text-blue-700 mt-2";
        normInsightElement.innerText = normInsight;
        document.getElementById('radarChart').after(normInsightElement);

    </script>
</x-app-layout>
