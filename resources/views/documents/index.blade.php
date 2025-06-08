<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Download PDF dengan Laravel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .item-card { 
            transition: all 0.2s ease-in-out;
            border-left: 4px solid transparent;
        }
        .item-card:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-left-color: #3b82f6;
        }
        input[type="checkbox"]:checked + label { 
            border-color: #3b82f6; 
            background-color: #f0f9ff;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1), 0 2px 4px -1px rgba(59, 130, 246, 0.06);
        }
        .filter-btn {
            transition: all 0.2s ease-in-out;
            position: relative;
        }
        .filter-btn.active { 
            background-color: #3b82f6; 
            color: white;
        }
        .filter-btn.active:after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 16px;
            height: 2px;
            background-color: #3b82f6;
            border-radius: 2px;
        }
        .download-btn {
            background-image: linear-gradient(to right, #3b82f6, #6366f1);
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3), 0 2px 4px -1px rgba(59, 130, 246, 0.1);
            transition: all 0.3s ease;
        }
        .download-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3), 0 4px 6px -2px rgba(59, 130, 246, 0.1);
        }
        .download-btn:active {
            transform: translateY(0);
        }
        .badge {
            font-size: 0.75rem;
            letter-spacing: 0.025em;
        }
        .empty-state {
            background-color: #f8fafc;
            border: 2px dashed #e2e8f0;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Header Navigasi -->
    <header class="bg-white shadow-sm w-full sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h1 class="text-xl font-bold text-gray-800">DocManager</h1>
            </div>
            <div class="flex items-center space-x-4">
                <button class="p-2 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium">
                    U
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 md:p-8 border-b border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h2 class="text-2xl font-bold text-gray-800">Unduh Dokumen Kumpulan</h2>
                        <p class="text-gray-600 mt-1">Pilih dokumen yang ingin Anda unduh sebagai file ZIP.</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
                            {{ count($documents) }} Dokumen
                        </span>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6 md:p-8">
                {{-- Menampilkan pesan error dari session --}}
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Menampilkan error validasi --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Terdapat {{ count($errors) }} error dalam pengisian</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            
                <form id="download-form" action="{{ route('documents.download') }}" method="POST">
                    @csrf
                    
                    <!-- Menu Aksi dan Filter -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <!-- Checkbox Pilih Semua -->
                        <div class="flex items-center">
                            <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                            <label for="select-all" class="ml-2 block text-sm font-medium text-gray-700">Pilih Semua / Batal Pilih</label>
                        </div>
                        <!-- Tombol Filter -->
                        <div id="filter-container" class="flex items-center space-x-2 bg-gray-50 p-1 rounded-lg">
                            <button type="button" data-filter="all" class="filter-btn active px-4 py-1.5 text-sm font-medium rounded-md">Semua</button>
                            <button type="button" data-filter="Invoice" class="filter-btn px-4 py-1.5 text-sm font-medium rounded-md">Invoice</button>
                            <button type="button" data-filter="Laporan" class="filter-btn px-4 py-1.5 text-sm font-medium rounded-md">Laporan</button>
                            <button type="button" data-filter="Surat" class="filter-btn px-4 py-1.5 text-sm font-medium rounded-md">Surat</button>
                        </div>
                    </div>

                    <div id="document-list" class="space-y-3">
                        @forelse ($documents as $id => $doc)
                            <div class="item-card" data-type="{{ $doc['tipe'] }}">
                                <input type="checkbox" name="item_ids[]" value="{{ $id }}" id="item-{{ $id }}" class="hidden item-checkbox">
                                <label for="item-{{ $id }}" class="block border border-gray-200 rounded-lg p-5 cursor-pointer hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                @if($doc['tipe'] === 'Invoice')
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                                        </svg>
                                                    </div>
                                                @elseif($doc['tipe'] === 'Laporan')
                                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-800">
                                                    @if($doc['tipe'] === 'Invoice') Invoice #{{ $doc['nomor'] }}
                                                    @elseif($doc['tipe'] === 'Laporan') {{ $doc['judul'] }}
                                                    @else {{ $doc['perihal'] }}
                                                    @endif
                                                </h3>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    @if($doc['tipe'] === 'Invoice') 
                                                        <span class="flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            {{ $doc['pelanggan'] }}
                                                        </span>
                                                    @elseif($doc['tipe'] === 'Surat') 
                                                        <span class="flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Surat Resmi
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="badge px-2.5 py-1 rounded-full
                                                @if($doc['tipe'] === 'Invoice') bg-blue-100 text-blue-800
                                                @elseif($doc['tipe'] === 'Laporan') bg-green-100 text-green-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $doc['tipe'] }}
                                            </span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @empty
                            <div class="empty-state rounded-lg p-8 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada dokumen</h3>
                                <p class="mt-1 text-sm text-gray-500">Belum ada dokumen yang tersedia untuk diunduh.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pesan Error -->
                    <div id="error-message" class="hidden mt-4 p-4 bg-red-50 rounded-lg border border-red-100">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-red-700 font-medium">Pilih setidaknya satu dokumen untuk diunduh.</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" id="download-button" class="download-btn w-full text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2 animate-spin hidden" id="spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="button-text">Unduh Dokumen Terpilih (.zip)</span>
                        </button>
                    </div>
                </form>
            </div>
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
                    const label = checkbox.nextElementSibling;
                    if (checkbox.checked) {
                        label.classList.add('border-blue-500', 'bg-blue-50');
                    } else {
                        label.classList.remove('border-blue-500', 'bg-blue-50');
                    }
                });
            });

            // Fungsi untuk update tampilan checkbox individual
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const label = this.nextElementSibling;
                    if (this.checked) {
                        label.classList.add('border-blue-500', 'bg-blue-50');
                    } else {
                        label.classList.remove('border-blue-500', 'bg-blue-50');
                    }
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
                    
                    // Uncheck select all ketika filter diubah
                    selectAllCheckbox.checked = false;
                    
                    // Update tampilan checkbox yang terlihat
                    document.querySelectorAll('.item-card:not(.hidden) .item-checkbox').forEach(checkbox => {
                        const label = checkbox.nextElementSibling;
                        if (checkbox.checked) {
                            label.classList.add('border-blue-500', 'bg-blue-50');
                        } else {
                            label.classList.remove('border-blue-500', 'bg-blue-50');
                        }
                    });
                }
            });

            // Validasi sebelum submit
            downloadForm.addEventListener('submit', function (event) {
                const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
                
                if (checkedCheckboxes.length === 0) {
                    event.preventDefault();
                    errorMessage.classList.remove('hidden');
                    downloadButton.classList.remove('download-btn');
                    downloadButton.classList.add('bg-gray-300', 'cursor-not-allowed');
                } else {
                    errorMessage.classList.add('hidden');
                    buttonText.textContent = 'Mempersiapkan unduhan...';
                    spinner.classList.remove('hidden');
                    downloadButton.setAttribute('disabled', 'true');
                    downloadButton.classList.add('cursor-not-allowed', 'opacity-75');
                }
            });
        });
    </script>
</body>
</html>