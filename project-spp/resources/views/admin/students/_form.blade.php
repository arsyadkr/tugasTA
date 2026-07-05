{{-- resources/views/admin/students/_form.blade.php --}}
{{-- Dipakai oleh create.blade.php dan edit.blade.php --}}

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- ── DATA SISWA ── --}}
    <div class="lg:col-span-2">
        <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-100">Data Siswa</h3>
    </div>

    {{-- Nama --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $student->name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- NIS --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">NIS <span class="text-red-500">*</span></label>
        <input type="text" name="nis" value="{{ old('nis', $student->nis ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('nis')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Jenis Kelamin --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
        <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Pilih --</option>
            <option value="L" {{ old('gender', $student->gender ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('gender', $student->gender ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
        @error('gender')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Kelas --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
        <select name="class_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Pilih Kelas --</option>
            @foreach ($classes as $c)
                <option value="{{ $c->id }}" {{ old('class_id', $student->class_id ?? '') == $c->id ? 'selected' : '' }}>
                    {{ $c->name }} ({{ $c->academic_year }})
                </option>
            @endforeach
        </select>
        @error('class_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Jurusan --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan <span class="text-red-500">*</span></label>
        <select name="major_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Pilih Jurusan --</option>
            @foreach ($majors as $m)
                <option value="{{ $m->id }}" {{ old('major_id', $student->major_id ?? '') == $m->id ? 'selected' : '' }}>
                    {{ $m->name }}
                </option>
            @endforeach
        </select>
        @error('major_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- No. HP Siswa --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP Siswa</label>
        <input type="text" name="phone" value="{{ old('phone', $student->phone ?? '') }}"
               placeholder="08xxxxxxxxxx"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Alamat --}}
    <div class="lg:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
        <textarea name="address" rows="2"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('address', $student->address ?? '') }}</textarea>
        @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- ── DATA ORANG TUA (untuk reminder) ── --}}
    <div class="lg:col-span-2">
        <h3 class="text-sm font-bold text-gray-700 mb-1 pb-2 border-b border-gray-100 mt-2">
            Data Orang Tua / Wali
            <span class="text-xs font-normal text-blue-600 ml-2">— untuk reminder tagihan via WhatsApp & Email</span>
        </h3>
    </div>

    {{-- Nama Orang Tua --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua / Wali</label>
        <input type="text" name="parent_name" value="{{ old('parent_name', $student->parent_name ?? '') }}"
               placeholder="Nama lengkap orang tua"
               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('parent_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- No. WA Orang Tua --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            No. WhatsApp Orang Tua
            <span class="text-xs text-gray-400 ml-1">(untuk reminder tagihan)</span>
        </label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">📱</span>
            <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone ?? '') }}"
                   placeholder="08xxxxxxxxxx atau 628xxxxxxxxxx"
                   class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        @error('parent_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Email Orang Tua --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Email Orang Tua
            <span class="text-xs text-gray-400 ml-1">(untuk reminder tagihan)</span>
        </label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">✉️</span>
            <input type="email" name="parent_email" value="{{ old('parent_email', $student->parent_email ?? '') }}"
                   placeholder="email@example.com"
                   class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        @error('parent_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

</div>