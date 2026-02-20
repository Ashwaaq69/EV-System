<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $session_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none; }
            body { background: white; margin: 0; padding: 0; }
            .receipt-card { border: none !important; shadow: none !important; }
        }
    </style>
    <script>
        // Trigger print after page is fully loaded
        window.onload = function() {
            setTimeout(function() {
                // Only trigger if not already printing
                if (!window.matchMedia('print').matches) {
                    // window.print(); 
                }
            }, 500);
        };
    </script>
</head>
<body class="bg-gray-50 flex items-center justify-center p-4 min-h-screen">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden border border-gray-100 receipt-card">
        <!-- Header -->
        <div class="bg-[#FF2D20] p-8 text-white relative">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tighter">CITRINE OS</h1>
                    <p class="text-white/80 text-sm mt-1">Charging Network Receipt</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold uppercase tracking-widest text-white/60">Invoice Date</p>
                    <p class="text-lg font-bold">{{ $date }}</p>
                </div>
            </div>
            
            <div class="mt-8 flex justify-between items-end">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/60">Reference Number</p>
                    <p class="text-2xl font-mono font-bold">#{{ $session_id }}</p>
                </div>
                <div class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20">
                    <span class="text-xs font-bold uppercase">Status: {{ $status }}</span>
                </div>
            </div>

            <!-- Absolute decorative circle -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full"></div>
        </div>

        <div class="p-8 space-y-8">
            <!-- Details Grid -->
            <div class="grid grid-cols-3 gap-6 py-6 border-b border-gray-100">
                <div>
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Bill To</h3>
                    <p class="font-bold text-gray-900">{{ $customer ?? 'Citrine Member' }}</p>
                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-tighter italic">Verified Payment</p>
                </div>
                <div>
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Vehicle Details</h3>
                    <p class="font-bold text-gray-900">{{ $vehicle_name }}</p>
                    <p class="text-sm font-mono text-gray-500 mt-1">{{ $vehicle_plate }}</p>
                </div>
                <div class="text-right">
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Charging Location</h3>
                    <p class="font-bold text-gray-900 text-sm">{{ $location }}</p>
                    <p class="text-[10px] text-gray-500 mt-1 truncate ml-auto max-w-[150px]">{{ $address }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 py-6 border-b border-gray-100">
                <div>
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Charger Info</h3>
                    <p class="text-sm font-semibold text-gray-900">{{ $charger }}</p>
                    <p class="text-xs text-gray-500 mt-1">Session started at {{ $time }}</p>
                </div>
                <div>
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Energy Summary</h3>
                    <p class="text-sm font-bold text-gray-900">{{ number_format($energy_kwh, 2) }} kWh Consumed</p>
                    <p class="text-xs text-gray-500 mt-1">Duration: {{ $duration ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Pricing Table -->
            <div>
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-gray-100 italic">
                            <th class="py-4 text-[10px] font-bold text-gray-400 uppercase">Service</th>
                            <th class="py-4 text-[10px] font-bold text-gray-400 uppercase text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr>
                            <td class="py-4 text-sm font-medium text-gray-700">EV Charging Session ({{ number_format($energy_kwh, 2) }} kWh)</td>
                            <td class="py-4 text-sm font-bold text-gray-900 text-right">{{ $currency }} {{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @if($discount > 0)
                        <tr>
                            <td class="py-4 text-sm font-medium text-green-600">Plan Discount ({{ $discount_percentage }}%)</td>
                            <td class="py-4 text-sm font-bold text-green-600 text-right">- {{ $currency }} {{ number_format($discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="py-4 text-sm font-medium text-gray-500">Service Tax (SST 8%)</td>
                            <td class="py-4 text-sm font-bold text-gray-900 text-right">{{ $currency }} {{ number_format($tax, 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td class="p-4 text-lg font-bold text-gray-900 rounded-l-2xl">Total Paid</td>
                            <td class="p-4 text-2xl font-black text-[#FF2D20] text-right rounded-r-2xl">{{ $currency }} {{ number_format($total_rm, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer Note -->
            <div class="text-center pt-8">
                <p class="text-xs text-gray-400">This is a computer-generated receipt. No signature is required.</p>
                <div class="mt-6 flex justify-center gap-4 no-print">
                    <button id="print-button" onclick="window.print()" class="bg-gray-900 text-white px-6 py-2 rounded-full text-sm font-bold hover:bg-gray-800 transition-colors">Print Receipt</button>
                    <button onclick="window.close()" class="border border-gray-200 text-gray-500 px-6 py-2 rounded-full text-sm font-bold hover:bg-gray-50 transition-colors">Close</button>
                </div>
            </div>
        </div>
        
        <!-- Bottom Zigzag effect (optional) -->
        <div class="h-4 w-full bg-[linear-gradient(135deg,#f3f4f6_25%,transparent_25%),linear-gradient(225deg,#f3f4f6_25%,transparent_25%)] bg-[length:20px_20px] bg-repeat-x"></div>
    </div>
</body>
</html>
