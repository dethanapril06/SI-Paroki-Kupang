<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Umat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class KeluargaSayaController extends Controller
{
    private function logoPdfSrc(?string $path): ?string
    {
        $path = $path ? trim($path, " \t\n\r\0\x0B\"'") : null;

        if (! $path || ! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => 'image/' . $extension,
        };

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    /**
     * Halaman detail keluarga milik user yang login.
     * Semua umat portal bisa lihat keluarganya,
     * tapi tombol "Tambah Anggota" hanya muncul untuk kepala keluarga.
     */
    public function show()
    {
        $umatId = Auth::user()->umat_id;

        if (!$umatId) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Data umat belum terhubung ke akun ini.');
        }

        $umat = Umat::aktif()->find($umatId);

        if (!$umat || !$umat->keluarga_id) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Anda belum terdaftar dalam keluarga manapun.');
        }

        $keluarga = Keluarga::with([
            'kepalaKeluarga' => fn($q) => $q->aktif(),
            'kub.wilayah',
            'umat' => fn($q) => $q->aktif()->orderByRaw("FIELD(hubungan_keluarga, 'Suami','Istri','Ayah','Ibu','Anak','Saudara','Lainnya')")->orderBy('tanggal_lahir'),
        ])->findOrFail($umat->keluarga_id);

        $isKetuaKeluarga = (int) $keluarga->kepala_keluarga_id === (int) $umatId;

        return view('portal.keluarga-saya.show', compact('keluarga', 'umat', 'isKetuaKeluarga'));
    }

    /**
     * Form edit data keluarga — hanya kepala keluarga.
     */
    public function edit()
    {
        $keluarga = $this->getKeluargaSaya();
        $this->authorizeKepala($keluarga);

        $anggota = $keluarga->umat()->aktif()->orderBy('nama')->get();

        return view('portal.keluarga-saya.edit', compact('keluarga', 'anggota'));
    }

    /**
     * Simpan perubahan data keluarga — hanya kepala keluarga.
     */
    public function update(Request $request)
    {
        $keluarga = $this->getKeluargaSaya();
        $this->authorizeKepala($keluarga);

        $validated = $request->validate([
            'alamat'                => ['required', 'string', 'max:500'],
            'status_tempat_tinggal' => ['required', 'in:Rumah Pribadi,Kontrak/Kost,Dinas'],
            'kepala_keluarga_id'    => ['nullable', 'exists:umat,id'],
        ]);

        // Pastikan kepala keluarga baru adalah anggota keluarga ini
        if (!empty($validated['kepala_keluarga_id'])) {
            $isAnggota = Umat::where('id', $validated['kepala_keluarga_id'])
                ->aktif()
                ->where('keluarga_id', $keluarga->id)
                ->exists();

            if (!$isAnggota) {
                return back()->withErrors([
                    'kepala_keluarga_id' => 'Kepala keluarga harus merupakan anggota keluarga ini.',
                ]);
            }
        }

        $keluarga->update($validated);

        return redirect()
            ->route('portal.keluarga-saya.show')
            ->with('success', 'Data keluarga berhasil diperbarui.');
    }

    /**
     * Helper: ambil keluarga berdasarkan umat_id user.
     */
    private function getKeluargaSaya(): Keluarga
    {
        $umatId = Auth::user()->umat_id;
        $umat   = Umat::aktif()->find($umatId);

        if (!$umat || !$umat->keluarga_id) {
            abort(404, 'Data keluarga tidak ditemukan.');
        }

        return Keluarga::with([
            'kepalaKeluarga' => fn($q) => $q->aktif(),
            'kub.wilayah',
            'umat' => fn($q) => $q->aktif()->orderBy('nama'),
        ])->findOrFail($umat->keluarga_id);
    }

    /**
     * Helper: pastikan user adalah kepala keluarga.
     */
    private function authorizeKepala(Keluarga $keluarga): void
    {
        if ((int) $keluarga->kepala_keluarga_id !== (int) Auth::user()->umat_id) {
            abort(403, 'Hanya kepala keluarga yang dapat mengedit data keluarga.');
        }
    }

    /**
     * Cetak Kartu Keluarga Katholik
     */
    public function cetak()
    {
        $keluarga = $this->getKeluargaSaya();

        // Muat relasi baptis untuk setiap anggota agar tersedia di view PDF
        $keluarga->umat->load('baptis');

        $logoKiri = $this->logoPdfSrc(config('dompdf.logo_kiri_path'));
        $logoKanan = $this->logoPdfSrc(config('dompdf.logo_kanan_path'));

        $pdf = Pdf::loadView('portal.keluarga-saya.pdf', compact('keluarga', 'logoKiri', 'logoKanan'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Kartu-Keluarga-Katholik-' . str_replace(' ', '-', $keluarga->kepalaKeluarga?->nama) . '.pdf');
    }
}
