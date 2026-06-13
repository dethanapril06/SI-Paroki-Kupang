<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Kub;
use App\Models\Umat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UmatController extends Controller
{
    private function getMyKub(): Kub
    {
        return Kub::where('ketua_umat_id', Auth::user()->umat_id)->firstOrFail();
    }

    private function authorizeUmat(Umat $umat): void
    {
        $myKub     = $this->getMyKub();
        $isInMyKub = Keluarga::where('id', $umat->keluarga_id)
            ->where('kub_id', $myKub->id)->exists();
        if (!$isInMyKub || $umat->status_keaktifan !== 'aktif') {
            abort(403, 'Anda tidak memiliki akses ke data umat ini.');
        }
    }

    public function index(Request $request)
    {
        $myKub = $this->getMyKub();
        $query = Umat::with(['keluarga', 'user'])
            ->aktif()
            ->whereHas('keluarga', fn($q) => $q->where('kub_id', $myKub->id))
            ->orderBy('nama');
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        $umat = $query->get();
        return view('portal.umat.index', compact('umat', 'myKub'));
    }

    public function show(Umat $umat)
    {
        $this->authorizeUmat($umat);
        $umat->load([
            'keluarga.kub.wilayah',
            'keluarga.kepalaKeluarga',
            'user',
            'kategorial',
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
        return view('portal.umat.show', compact('umat'));
    }

    public function create()
    {
        $myKub        = $this->getMyKub();
        $keluargaList = Keluarga::where('kub_id', $myKub->id)->orderBy('alamat')->get();
        return view('portal.umat.create', compact('keluargaList', 'myKub'));
    }

    public function store(Request $request)
    {
        $myKub     = $this->getMyKub();
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
            'email'                  => ['required', 'email', 'unique:users,email'],
        ]);

        $keluargaValid = Keluarga::where('id', $validated['keluarga_id'])
            ->where('kub_id', $myKub->id)->exists();
        if (!$keluargaValid) {
            return back()->withErrors(['keluarga_id' => 'Keluarga yang dipilih tidak berada dalam KUB Anda.']);
        }

        $validated['penyandang_disabilitas'] = $request->boolean('penyandang_disabilitas');
        $validated['status_almarhum']        = $request->boolean('status_almarhum');

        $umat = Umat::create(collect($validated)->except(['email'])->toArray());

        // Buat akun user + assign role 'umat'
        $user = User::create([
            'name'    => $umat->nama,
            'email'   => $validated['email'],
            'password'=> Hash::make('password'),
            'umat_id' => $umat->id,
        ]);

        $umatRole = DB::table('roles')->where('name', 'umat')->first();
        if ($umatRole) {
            DB::table('user_roles')->insertOrIgnore([
                'user_id'    => $user->id,
                'role_id'    => $umatRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()
            ->route('portal.umat.show', $umat)
            ->with('success', 'Data umat dan akun login berhasil dibuat.');
    }

    public function edit(Umat $umat)
    {
        $this->authorizeUmat($umat);
        $umat->load('keluarga');
        return view('portal.umat.edit', compact('umat'));
    }

    public function update(Request $request, Umat $umat)
    {
        $this->authorizeUmat($umat);
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
        ]);
        $validated['penyandang_disabilitas'] = $request->boolean('penyandang_disabilitas');
        $umat->update($validated);
        return redirect()
            ->route('portal.umat.show', $umat)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Umat $umat)
    {
        $this->authorizeUmat($umat);
        $umat->delete();
        return redirect()
            ->route('portal.umat.index')
            ->with('success', 'Data anggota berhasil dihapus.');
    }
}
