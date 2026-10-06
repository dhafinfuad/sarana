<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Out Form Pengambilan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background: white;
            }
            @page {
                size: A4 portrait;
                margin: 20mm;
            }
            /* Hide print dialog buttons if any */
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-white text-black font-sans min-h-screen">

    <div class="max-w-4xl p-5 mx-auto md:p-8">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b pb-4 mb-6">
            
            <div class="text-left mb-3">
                <h2 class="text-xl font-bold">Print Out Form Pengambilan</h2>
            </div>

            <div class="text-right mb-3">
                <div class="py-3 text-[13px] inline-block">
                    User : {{ $bonRequest->user->name ?? $bonRequest->user->role ?? '-' }}
                </div>
            </div>
        </div>

        <!-- Form Info -->
        <div class="mb-6">
            <h1 class="text-xl font-semibold">No.Form #{{ $bonRequest->no_bon }}</h1>
            <div class="text-sm font-semibold mt-1">Tanggal Form: {{ $bonRequest->created_at->format('Y-m-d') }}</div>
        </div>

        <!-- Table -->
        <table class="w-full text-left text-sm mb-8 min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="border-b border-gray-300 ">
                    <th class="py-3 px-3 font-semibold w-12 text-center">No</th>
                    <th class="py-3 px-3 font-semibold w-32 text-center">Kode. Barang</th>
                    <th class="py-3 px-3 font-semibold text-left">Nama Barang</th>
                    <th class="py-3 px-3 font-semibold w-24 text-right">Jumlah</th>
                    <th class="py-3 px-3 font-semibold w-24 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php $total = 0; @endphp
                @foreach($bonRequest->items as $index => $item)
                <tr>
                    <td class="py-3 px-3 text-center">{{ $index + 1 }}</td>
                    <td class="py-3 px-3 text-center">{{ $item->bonItem->kode_barang ?? '-' }}</td>
                    <td class="py-3 px-3 text-left">{{ $item->nama_barang }}</td>
                    <td class="py-3 px-3 text-right">{{ $item->jumlah_diminta }}</td>
                    <td class="py-3 px-3 text-right">{{ $item->jumlah_diminta }}</td>
                </tr>
                @php $total += $item->jumlah_diminta; @endphp
                @endforeach
            </tbody>
        </table>

        <!-- Footer / Signatures -->
        <hr class="mb-5">
        <div class="w-full mt-12" style="display: flex; flex-direction: column; align-items: center;">
            
            <div class="flex gap-4 px-3 font-bold mb-8">
                <div>Total Pengambilan :</div>
                <div class="text-right">{{ $total }}</div>
            </div>
            
            <div class="w-1/2">
                <div class="flex justify-between text-center gap-6 text-sm">
                    <div class="flex gap-8" style="flex-direction: column;">
                        <div class="mb-8">Penerima</div>
                        <div class="mt-8">(..................................)</div>
                    </div>
                    
                    <div class="flex gap-8" style="flex-direction: column;">
                        <div class="mb-8">Periksa oleh</div>
                        <div class="mt-8">(..................................)</div>
                    </div>
                    
                    <div class="flex gap-8" style="flex-direction: column;">
                        <div class="mb-8">Hormat Kami</div>
                        <div class="mt-8">(..................................)</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Print Automator -->
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
