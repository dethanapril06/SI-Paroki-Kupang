<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Umat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UmatController extends Controller
{
    /**
     * Daftar semua umat (standalone, bukan nested di keluarga).
     */
    public function index(Request $request)
    {
        $query = Umat::with(['keluarga.kub.wilayah', 'user'])
            ->aktif()
            ->orderBy('nama');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $umat = $query->get();

        return view('sekretariat.umat.index', compact('umat'));
    }

    /**
     * Detail satu umat.
     */
    public function show(Umat $umat)
    {
        abort_unless($umat->status_keaktifan === 'aktif', 404);

        $umat->load([
            'keluarga.kub.wilayah',
            'user',
            'kategorial',
            'kubDiketuai',
            'kategorialDiketuai',
            'sakramen.paroki',
            'sakramen.klerus',
            'sakramen.baptis.klerus',
            'sakramen.baptis.bapakBaptis',
            'sakramen.baptis.ibuBaptis',
            'sakramen.komuniPertama',
            'sakramen.krisma.uskup',
            'sakramen.pernikahan.pasangan',
            'sakramen.minyakSuci',
        ]);

        return view('sekretariat.umat.show', compact('umat'));
    }

    /**
     * Form tambah umat standalone — keluarga dipilih dari dropdown.
     */
    public function createStandalone()
    {
        $keluargaList = Keluarga::with('kub.wilayah')->orderBy('alamat')->get();

        return view('sekretariat.umat.create', compact('keluargaList'));
    }

    /**
     * Simpan umat baru dari form standalone.
     */
    public function storeStandalone(Request $request)
    {
        $validated = $request->validate([
            'keluarga_id'            => ['required', 'exists:keluarga,id'],
            'nama'                   => ['required', 'string', 'max:255'],
            'tempat_lahir'           => ['required', 'string', 'max:255'],
            'tanggal_lahir'          => ['required', 'date'],
            'jenis_kelamin'          => ['required', 'in:Laki-laki,Perempuan'],
            'hubungan_keluarga'      => ['required', 'in:Suami,Istri,Anak,Saudara,Ayah,Ibu,Lainnya'],
            'nama_ayah'              => ['nullable', 'string', 'max:255'],
            'nama_ibu'               => ['nullable', 'string', 'max:255'],
            'status_pernikahan'      => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'no_telepon'             => ['nullable', 'string', 'max:20'],
            'golongan_darah'         => ['nullable', 'in:A,B,AB,O'],
            'pendidikan'             => ['nullable', 'in:Tidak Sekolah,SD,SMP,SMA,D3,S1,S2,S3'],
            'pekerjaan'              => ['nullable', 'string', 'max:255'],
            'penyandang_disabilitas' => ['boolean'],
            'status_almarhum'        => ['boolean'],
            'keterangan_lain'        => ['boolean'],
            'email'                  => ['required', 'email', 'unique:users,email'],
        ]);

        $validated['penyandang_disabilitas'] = $request->boolean('penyandang_disabilitas');
        $validated['status_almarhum']        = $request->boolean('status_almarhum');
        $validated['keterangan_lain']        = $request->boolean('keterangan_lain');

        $umat = Umat::create(
            collect($validated)->except(['email', 'password', 'password_confirmation'])->toArray()
        );

        $user = User::create([
            'name'     => $umat->nama,
            'email'    => $validated['email'],
            'password' => Hash::make('password'),
            'umat_id'  => $umat->id,
        ]);

        // Assign default role 'umat'
        $roleId = DB::table('roles')->where('name', 'umat')->value('id');
        if ($roleId) {
            DB::table('user_roles')->insertOrIgnore([
                'user_id'    => $user->id,
                'role_id'    => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()
            ->route('sekretariat.umat.show', $umat)
            ->with('success', 'Data umat dan akun login berhasil dibuat.');
    }

    /**
     * Tampilkan form tambah anggota untuk keluarga tertentu (nested dari keluarga show).
     */
    public function create(Keluarga $keluarga)
    {
        $keluarga->load('kub.wilayah');

        return view('sekretariat.umat.create', compact('keluarga'));

    }

    /**
     * Simpan anggota baru ke dalam keluarga.
     */
    public function store(Request $request, Keluarga $keluarga)
    {
        $validated = $request->validate([
            'nama'                   => ['required', 'string', 'max:255'],
            'tempat_lahir'           => ['required', 'string', 'max:255'],
            'tanggal_lahir'          => ['required', 'date'],
            'jenis_kelamin'          => ['required', 'in:Laki-laki,Perempuan'],
            'hubungan_keluarga'      => ['required', 'in:Suami,Istri,Anak,Saudara,Ayah,Ibu,Lainnya'],
            'nama_ayah'              => ['nullable', 'string', 'max:255'],
            'nama_ibu'               => ['nullable', 'string', 'max:255'],
            'status_pernikahan'      => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'no_telepon'             => ['nullable', 'string', 'max:20'],
            'golongan_darah'         => ['nullable', 'in:A,B,AB,O'],
            'pendidikan'             => ['nullable', 'in:Tidak Sekolah,SD,SMP,SMA,D3,S1,S2,S3'],
            'pekerjaan'              => ['nullable', 'string', 'max:255'],
            'penyandang_disabilitas' => ['boolean'],
            'status_almarhum'        => ['boolean'],
            'keterangan_lain'        => ['boolean'],
            // Akun login
            'email'                  => ['required', 'email', 'unique:users,email'],
        ]);

        $validated['keluarga_id']            = $keluarga->id;
        $validated['penyandang_disabilitas'] = $request->boolean('penyandang_disabilitas');
        $validated['status_almarhum']        = $request->boolean('status_almarhum');
        $validated['keterangan_lain']        = $request->boolean('keterangan_lain');

        $umat = Umat::create(
            collect($validated)->except(['email', 'password', 'password_confirmation'])->toArray()
        );

        // Otomatis buat akun login untuk umat (default password: 'password')
        $user = User::create([
            'name'     => $umat->nama,
            'email'    => $validated['email'],
            'password' => Hash::make('password'),
            'umat_id'  => $umat->id,
        ]);

        // Assign default role 'umat'
        $roleId = DB::table('roles')->where('name', 'umat')->value('id');
        if ($roleId) {
            DB::table('user_roles')->insertOrIgnore([
                'user_id'    => $user->id,
                'role_id'    => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()
            ->route('sekretariat.keluarga.show', $keluarga)
            ->with('success', 'Data anggota dan akun login berhasil dibuat.');
    }

    /**
     * Tampilkan form edit anggota.
     */
    public function edit(Umat $umat)
    {
        $umat->load('keluarga.kub.wilayah');

        return view('sekretariat.umat.edit', compact('umat'));
    }

    /**
     * Simpan perubahan data anggota.
     */
    public function update(Request $request, Umat $umat)
    {
        $validated = $request->validate([
            'nama'                  => ['required', 'string', 'max:255'],
            'tempat_lahir'          => ['required', 'string', 'max:255'],
            'tanggal_lahir'         => ['required', 'date'],
            'jenis_kelamin'         => ['required', 'in:Laki-laki,Perempuan'],
            'hubungan_keluarga'     => ['required', 'in:Suami,Istri,Anak,Saudara,Ayah,Ibu,Lainnya'],
            'nama_ayah'             => ['nullable', 'string', 'max:255'],
            'nama_ibu'              => ['nullable', 'string', 'max:255'],
            'status_pernikahan'     => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'no_telepon'            => ['nullable', 'string', 'max:20'],
            'golongan_darah'        => ['nullable', 'in:A,B,AB,O'],
            'pendidikan'            => ['nullable', 'in:Tidak Sekolah,SD,SMP,SMA,D3,S1,S2,S3'],
            'pekerjaan'             => ['nullable', 'string', 'max:255'],
            'penyandang_disabilitas' => ['boolean'],
            'status_almarhum'       => ['boolean'],
            'keterangan_lain'       => ['boolean'],
        ]);

        $validated['penyandang_disabilitas'] = $request->boolean('penyandang_disabilitas');
        $validated['status_almarhum']        = $request->boolean('status_almarhum');
        $validated['keterangan_lain']        = $request->boolean('keterangan_lain');

        $umat->update($validated);

        return redirect()
            ->route('sekretariat.keluarga.show', $umat->keluarga_id)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Hapus anggota dari keluarga.
     */
    public function destroy(Umat $umat)
    {
        $keluargaId = $umat->keluarga_id;
        
        // Hapus akun login yang terikat jika ada
        User::where('umat_id', $umat->id)->delete();
        
        $umat->delete();

        return redirect()
            ->route('sekretariat.keluarga.show', $keluargaId)
            ->with('success', 'Data anggota dan akun login terkait berhasil dihapus.');
    }
}
