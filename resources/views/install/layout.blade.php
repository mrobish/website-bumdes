<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi BUMDes Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .step-active { background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); }
        .step-done { background: #10b981; }
        .step-pending { background: #e5e7eb; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <div class="bg-white shadow-sm">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">BUMDes Management System</h1>
                        <p class="text-sm text-gray-500">Wizard Instalasi</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border-b">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    @php
                        $steps = [
                            ['num' => 1, 'label' => 'Sistem Check'],
                            ['num' => 2, 'label' => 'Database'],
                            ['num' => 3, 'label' => 'Admin'],
                            ['num' => 4, 'label' => 'Identitas BUMDes'],
                            ['num' => 5, 'label' => 'Selesai'],
                        ];
                        $currentStep = $currentStep ?? 1;
                    @endphp

                    @foreach($steps as $step)
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white
                                @if($step['num'] < $currentStep) step-done
                                @elseif($step['num'] == $currentStep) step-active
                                @else step-pending text-gray-500 @endif">
                                @if($step['num'] < $currentStep)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    {{ $step['num'] }}
                                @endif
                            </div>
                            <span class="text-sm font-medium hidden sm:inline {{ $step['num'] == $currentStep ? 'text-blue-600' : 'text-gray-500' }}">
                                {{ $step['label'] }}
                            </span>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 mx-2 {{ $step['num'] < $currentStep ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex-1 py-8">
            <div class="max-w-4xl mx-auto px-4">
                @yield('content')
            </div>
        </div>

        <div class="bg-white border-t py-4">
            <div class="max-w-4xl mx-auto px-4 text-center text-sm text-gray-500">
                <p>{{ date('Y') }} mrobis - 
                    <a href="https://github.com/mrobis/website-bumdes" target="_blank" class="text-blue-600 hover:text-blue-800">GitHub</a>
                </p>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
