
@php
    $cls = 'w-full border rounded px-3 py-2';
    $roleSaatIni = old('role', $user->role?->value ?? 'sekolah');
@endphp

<div class="bg-white rounded shadow p-6 max-w-2xl space-y-4">

    {{-- Nama --}}
    <div>
        <label for="name" class="block font-medium">Nama</label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $user->name) }}"
               class="{{ $cls }}">

        @error('name')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div>
        <label for="email" class="block font-medium">Email</label>
        <input type="email" id="email" name="email"
               value="{{ old('email', $user->email) }}"
               class="{{ $cls }}">

        @error('email')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Role --}}
    <div>
        <label for="role" class="block font-medium">Role</label>

        <select id="role" name="role" class="{{ $cls }}">
            @foreach ([
                'admin' => 'Admin',
                'dinas' => 'Dinas Pendidikan',
                'sekolah' => 'Sekolah'
            ] as $nilai => $label)
                <option value="{{ $nilai }}"
                    @selected($roleSaatIni === $nilai)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('role')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Pilihan Sekolah --}}
    <div id="blok-sekolah">
        <label for="sekolah_id" class="block font-medium">Sekolah</label>

        <select id="sekolah_id" name="sekolah_id" class="{{ $cls }}">
            <option value="">-- Pilih sekolah --</option>

            @foreach ($sekolahs as $s)
                <option value="{{ $s->id }}"
                    @selected((int) old('sekolah_id', $user->sekolah_id) === $s->id)>
                    {{ $s->npsn }} - {{ $s->nama }}
                </option>
            @endforeach
        </select>

        @error('sekolah_id')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status Akun --}}
    <div>
        <label for="is_active" class="block font-medium">Status Akun</label>

        <select id="is_active" name="is_active" class="{{ $cls }}">
            <option value="1"
                @selected((string) old('is_active', $user->exists ? (int) $user->is_active : 1) === '1')>
                Aktif
            </option>

            <option value="0"
                @selected((string) old('is_active', $user->exists ? (int) $user->is_active : 1) === '0')>
                Nonaktif
            </option>
        </select>

        <p class="text-gray-500 text-sm">
            Akun nonaktif tidak dapat melakukan login.
        </p>

        @error('is_active')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kata Sandi --}}
    <div>
        <label for="password" class="block font-medium">Kata Sandi</label>

        <input type="password" id="password" name="password"
               class="{{ $cls }}" autocomplete="new-password">

        @if ($user->exists)
            <p class="text-gray-500 text-sm">
                Kosongkan jika tidak diganti.
            </p>
        @endif

        @error('password')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- Konfirmasi Kata Sandi --}}
    <div>
        <label for="password_confirmation" class="block font-medium">
            Ulangi Kata Sandi
        </label>

        <input type="password" id="password_confirmation"
               name="password_confirmation"
               class="{{ $cls }}" autocomplete="new-password">
    </div>

    {{-- Tombol --}}
    <div class="flex gap-3 pt-2">
        <button type="submit"
                class="px-5 py-2 bg-blue-700 text-white rounded hover:bg-blue-800">
            Simpan
        </button>

        <a href="{{ route('admin.users.index') }}"
           class="px-5 py-2 bg-gray-300 rounded hover:bg-gray-400">
            Batal
        </a>
    </div>
</div>

{{-- JavaScript pilihan sekolah --}}
@push('scripts')
<script>
    const role = document.getElementById('role');
    const blok = document.getElementById('blok-sekolah');

    function aturBlokSekolah() {
        blok.classList.toggle('hidden', role.value !== 'sekolah');
    }

    role.addEventListener('change', aturBlokSekolah);
    aturBlokSekolah();
</script>
@endpush
