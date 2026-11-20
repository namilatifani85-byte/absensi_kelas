@extends('layouts.app')

@section('title', 'Edit Kelas: ' . $class->code)

@section('content')
<div style="max-width: 40rem; margin: 0 auto;">

    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('classes.index') }}" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: #2563EB; text-decoration: none; font-weight: 500; margin-bottom: 0.5rem;">
            &larr; Kembali ke Daftar Kelas
        </a>
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #0F172A; margin: 0;">Sunting Kelas Perkuliahan</h1>
        <p style="font-size: 0.875rem; color: #475569; margin: 0.25rem 0 0 0;">
            Perbarui informasi mata kuliah {{ $class->code }}.
        </p>
    </div>

    <div class="card-surface" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 2rem;">
        <form action="{{ route('classes.update', $class) }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')

            {{-- Kode Kelas --}}
            <div>
                <label for="code" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.375rem;">
                    Kode Kelas <span style="color: #DC2626;">*</span>
                </label>
                <input type="text"
                       id="code"
                       name="code"
                       value="{{ old('code', $class->code) }}"
                       required
                       class="focus-accessible"
                       style="width: 100%; box-sizing: border-box; padding: 0.625rem 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem;">
                @error('code')
                    <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Nama Mata Kuliah --}}
            <div>
                <label for="name" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.375rem;">
                    Nama Mata Kuliah <span style="color: #DC2626;">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $class->name) }}"
                       required
                       class="focus-accessible"
                       style="width: 100%; box-sizing: border-box; padding: 0.625rem 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem;">
                @error('name')
                    <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Ruangan --}}
            <div>
                <label for="room" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.375rem;">
                    Ruangan Perkuliahan <span style="color: #DC2626;">*</span>
                </label>
                <input type="text"
                       id="room"
                       name="room"
                       value="{{ old('room', $class->room) }}"
                       required
                       class="focus-accessible"
                       style="width: 100%; box-sizing: border-box; padding: 0.625rem 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem;">
                @error('room')
                    <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Hari --}}
            <div>
                <label for="day" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.375rem;">
                    Hari Perkuliahan <span style="color: #DC2626;">*</span>
                </label>
                <select id="day"
                        name="day"
                        required
                        class="focus-accessible"
                        style="width: 100%; box-sizing: border-box; padding: 0.625rem 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem; background-color: #FFFFFF;">
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                        <option value="{{ $hari }}" {{ old('day', $class->day) === $hari ? 'selected' : '' }}>
                            {{ $hari }}
                        </option>
                    @endforeach
                </select>
                @error('day')
                    <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Waktu Mulai & Selesai --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label for="time_start" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.375rem;">
                        Jam Mulai <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text"
                           id="time_start"
                           name="time_start"
                           value="{{ old('time_start', substr($class->time_start, 0, 5)) }}"
                           required
                           class="focus-accessible"
                           style="width: 100%; box-sizing: border-box; padding: 0.625rem 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem;">
                    @error('time_start')
                        <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="time_end" style="display: block; font-size: 0.875rem; font-weight: 600; color: #0F172A; margin-bottom: 0.375rem;">
                        Jam Selesai <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text"
                           id="time_end"
                           name="time_end"
                           value="{{ old('time_end', substr($class->time_end, 0, 5)) }}"
                           required
                           class="focus-accessible"
                           style="width: 100%; box-sizing: border-box; padding: 0.625rem 0.875rem; border: 1px solid #CBD5E1; border-radius: 0.5rem; font-size: 0.875rem;">
                    @error('time_end')
                        <span style="font-size: 0.75rem; color: #DC2626; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                <a href="{{ route('classes.index') }}"
                   style="padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; background-color: #FFFFFF; color: #475569; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                    Batal
                </a>
                <button type="submit"
                        class="focus-accessible"
                        style="padding: 0.625rem 1.5rem; border-radius: 0.5rem; border: none; background-color: #2563EB; color: #FFFFFF; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
