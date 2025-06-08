<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Download PDF dengan Laravel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .item-card { transition: all 0.2s ease-in-out; }
        .item-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        input[type="checkbox"]:checked + label { border-color: #3b82f6; background-color: #eff6ff; }
        .filter-btn.active { background-color: #3b82f6; color: white; }
        .filter-btn { transition: all 0.2s ease-in-out; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header Navigasi -->
    <header class="bg-white shadow-md w-full p-4 mb-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-xl font-bold text-gray-800">Manajemen Dokumen Laravel</h1>
        </div>
    </header>

    <main class="w-full max-w-4xl mx-auto px-4 pb-8">
        <div class="bg-white p-6 md:p-8 rounded-lg shadow-lg">
            <div class="text-center mb-6">
                 <h2 class="text-2xl font-bold text-gray-800">Unduh Dokumen Kumpulan</h2>
                 <p class="text-gray-500">Pilih dokumen yang ingin Anda unduh sebagai file ZIP.</p>
            </div>

            {{-- Menampilkan pesan error dari session --}}
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Menampilkan error validasi --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        
            <form id="download-form" action="{{ route('documents.download') }}" method="POST">
                @csrf {{-- Token CSRF Wajib di Laravel --}}
                
                <!-- Menu Aksi dan Filter -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <!-- Checkbox Pilih Semua -->
                    <div class="flex items-center">
                        <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="select-all" class="ml-2 block text-sm font-medium text-gray-700">Pilih Semua / Batal Pilih</label>
                    </div>
                    <!-- Tombol Filter -->
                    <div id="filter-container" class="flex items-center space-x-2 bg-gray-100 p-1 rounded-lg">
                        <button type="button" data-filter="all" class="filter-btn active px-3 py-1 text-sm font-semibold rounded-md">Semua</button>
                        <button type="button" data-filter="Invoice" class="filter-btn px-3 py-1 text-sm font-semibold rounded-md">Invoice</button>
                        <button type="button" data-filter="Laporan" class="filter-btn px-3 py-1 text-sm font-semibold rounded-md">Laporan</button>
                        <button type="button" data-filter="Surat" class="filter-btn px-3 py-1 text-sm font-semibold rounded-md">Surat</button>
                    </div>
                </div>

                <div id="document-list" class="space-y-4">
                    @forelse ($documents as $id => $doc)
                        <div class="item-card" data-type="{{ $doc['tipe'] }}">
                            <input type="checkbox" name="item_ids[]" value="{{ $id }}" id="item-{{ $id }}" class="hidden item-checkbox">
                            <label for="item-{{ $id }}" class="block border-2 border-gray-200 rounded-lg p-4 cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-700">
                                            @if($doc['tipe'] === 'Invoice') Invoice #{{ $doc['nomor'] }}
                                            @elseif($doc['tipe'] === 'Laporan') {{ $doc['judul'] }}
                                            @else {{ $doc['perihal'] }}
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            @if($doc['tipe'] === 'Invoice') Pelanggan: {{ $doc['pelanggan'] }}
                                            @elseif($doc['tipe'] === 'Surat') Tipe: Surat Resmi
                                            @endif
                                        </p>
                                    </div>
                                    <span class="text-sm font-medium px-2 py-1 rounded-full
                                        @if($doc['tipe'] === 'Invoice') bg-blue-100 text-blue-800
                                        @elseif($doc['tipe'] === 'Laporan') bg-green-100 text-green-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ $doc['tipe'] }}
                                    </span>
                                </div>
                            </label>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">Tidak ada dokumen yang tersedia.</p>
                    @endforelse
                </div>

                <!-- Pesan Error -->
                <div id="error-message" class="hidden mt-4 text-red-600 text-center font-medium">
                    Pilih setidaknya satu dokumen untuk diunduh.
                </div>

                <div class="mt-6">
                    <button type="submit" id="download-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 animate-spin hidden" id="spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="button-text">Unduh Dokumen Terpilih (.zip)</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all');
            const downloadForm = document.getElementById('download-form');
            const errorMessage = document.getElementById('error-message');
            const filterContainer = document.getElementById('filter-container');
            const downloadButton = document.getElementById('download-button');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');

            // Fungsi untuk "Pilih Semua"
            selectAllCheckbox.addEventListener('change', function () {
                const visibleCheckboxes = document.querySelectorAll('.item-card:not(.hidden) .item-checkbox');
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            // Fungsi untuk Filter
            filterContainer.addEventListener('click', function (e) {
                if (e.target.tagName === 'BUTTON') {
                    const filter = e.target.getAttribute('data-filter');
                    
                    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                    e.target.classList.add('active');

                    document.querySelectorAll('.item-card').forEach(card => {
                        if (filter === 'all' || card.getAttribute('data-type') === filter) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                    selectAllCheckbox.checked = false;
                }
            });

            // Validasi sebelum submit
            downloadForm.addEventListener('submit', function (event) {
                const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
                
                if (checkedCheckboxes.length === 0) {
                    event.preventDefault();
                    errorMessage.classList.remove('hidden');
                } else {
                    errorMessage.classList.add('hidden');
                    buttonText.textContent = 'Memproses...';
                    spinner.classList.remove('hidden');
                    downloadButton.setAttribute('disabled', 'true');
                    downloadButton.classList.add('cursor-not-allowed', 'bg-blue-400');
                }
            });
        });
    </script>
</body>
</html>