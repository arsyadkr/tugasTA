<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Generate Tagihan Khusus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('admin.partials.sidebar')
<main style="margin-left:220px">
@include('admin.partials.topbar', ['title' => 'Generate Tagihan Khusus'])
<div class="p-6 max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h1 class="text-base font-bold text-gray-800 mb-2">Generate Tagihan Pembayaran Khusus</h1>
        <p class="text-xs text-gray-400 mb-6">
            Tagihan akan otomatis dibuat untuk <strong>semua siswa</strong> sesuai kelas yang ditentukan per jenis pembayaran.
        </p>

        @if(session('error'))<div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ session('error') }}</div>@endif

        {{-- Info kelas per jenis --}}
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="p-3 rounded-xl text-center" style="background:linear-gradient(135deg,#667eea20,#764ba220);border:1px solid #667eea40">
                <p class="text-[10px] font-semibold text-purple-600 uppercase">Kunjungan Industri</p>
                <p class="text-2xl font-bold text-purple-700 mt-1">10</p>
                <p class="text-[10px] text-purple-500">Khusus kelas 10</p>
            </div>
            <div class="p-3 rounded-xl text-center" style="background:linear-gradient(135deg,#f093fb20,#f5576c20);border:1px solid #f5576c40">
                <p class="text-[10px] font-semibold text-pink-600 uppercase">GTS</p>
                <p class="text-2xl font-bold text-pink-700 mt-1">11</p>
                <p class="text-[10px] text-pink-500">Khusus kelas 11</p>
            </div>
            <div class="p-3 rounded-xl text-center" style="background:linear-gradient(135deg,#4facfe20,#00f2fe20);border:1px solid #4facfe40">
                <p class="text-[10px] font-semibold text-blue-600 uppercase">PKL</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">12</p>
                <p class="text-[10px] text-blue-500">Khusus kelas 12</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.extra.store') }}" novalidate>
            @csrf

            {{-- Jenis pembayaran --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="kunjungan_industri" class="sr-only peer"
                               {{ old('type')==='kunjungan_industri'?'checked':'' }}>
                        <div class="p-3 rounded-xl border-2 border-gray-200 text-center transition peer-checked:border-purple-500 peer-checked:bg-purple-50">
                            <p class="text-xs font-semibold text-gray-700">Kunjungan Industri</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Kelas 10</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="gts" class="sr-only peer"
                               {{ old('type')==='gts'?'checked':'' }}>
                        <div class="p-3 rounded-xl border-2 border-gray-200 text-center transition peer-checked:border-pink-500 peer-checked:bg-pink-50">
                            <p class="text-xs font-semibold text-gray-700">GTS</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Kelas 11</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="pkl" class="sr-only peer"
                               {{ old('type')==='pkl'?'checked':'' }}>
                        <div class="p-3 rounded-xl border-2 border-gray-200 text-center transition peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <p class="text-xs font-semibold text-gray-700">PKL</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Kelas 12</p>
                        </div>
                    </label>
                </div>
                @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tagihan <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Kunjungan Industri 2025"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="1000" placeholder="500000"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('due_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700 mb-5">
                ⚠️ Tagihan akan di-generate otomatis untuk <strong>semua siswa aktif</strong> sesuai kelas yang ditentukan. Siswa yang sudah memiliki tagihan jenis ini akan dilewati.
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700">
                    Generate Tagihan
                </button>
                <a href="{{ route('admin.extra.index') }}" class="px-5 py-2.5 text-sm text-gray-500 hover:text-gray-700">Batal</a>
            </div>
        </form>
    </div>
</div>
</main>
</body>
</html>