<?php

namespace App\Http\Controllers\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Klerus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KlerusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $klerus = Klerus::latest()->get();

        return view('sekretariat.klerus.index', compact('klerus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sekretariat.klerus.create');
    }

    /**
     * Store a newly created resource in storage.
     * Email akun login di-generate otomatis dari nama klerus.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'         => ['required', 'string', 'max:255'],
            'jabatan'      => ['required', Rule::in(['pastor', 'uskup'])],
            'status_aktif' => ['required', Rule::in(['Aktif', 'Meninggal', 'Emeritus'])],
        ]);

        $klerus = Klerus::create($validated);
        $email = null;

        if ($validated['jabatan'] === 'pastor') {
            // Generate email unik: nama.slug@klerus.paroki
            $email   = Str::slug($klerus->nama, '.') . '@klerus.paroki';
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = Str::slug($klerus->nama, '.') . $counter . '@klerus.paroki';
                $counter++;
            }

            $user = User::create([
                'name'      => $klerus->nama,
                'email'     => $email,
                'password'  => Hash::make('password'),
                'klerus_id' => $klerus->id,
            ]);

            // Assign role 'pastor'
            $roleId = DB::table('roles')->where('name', 'pastor')->value('id');
            if ($roleId) {
                DB::table('user_roles')->insertOrIgnore([
                    'user_id'    => $user->id,
                    'role_id'    => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $message = "Data klerus berhasil dibuat.";
        if ($email) {
            $message .= " Akun login: {$email} | Password: password";
        }

        return redirect()
            ->route('sekretariat.klerus.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Klerus $klerus)
    {
        $klerus->load(['keuskupan', 'paroki', 'kuasi']);

        $user = User::where('klerus_id', $klerus->id)->first();

        return view('sekretariat.klerus.show', compact('klerus', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Klerus $klerus)
    {
        return view('sekretariat.klerus.edit', compact('klerus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Klerus $klerus)
    {
        $validated = $request->validate([
            'nama'         => ['required', 'string', 'max:255'],
            'jabatan'      => ['required', Rule::in(['pastor', 'uskup'])],
            'status_aktif' => ['required', Rule::in(['Aktif', 'Meninggal', 'Emeritus'])],
        ]);

        $oldJabatan = $klerus->getOriginal('jabatan');
        $klerus->update($validated);

        if ($validated['jabatan'] === 'pastor') {
            $user = User::where('klerus_id', $klerus->id)->first();
            if (!$user) {
                // Generate email unik: nama.slug@klerus.paroki
                $email   = Str::slug($klerus->nama, '.') . '@klerus.paroki';
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = Str::slug($klerus->nama, '.') . $counter . '@klerus.paroki';
                    $counter++;
                }

                $user = User::create([
                    'name'      => $klerus->nama,
                    'email'     => $email,
                    'password'  => Hash::make('password'),
                    'klerus_id' => $klerus->id,
                ]);

                // Assign role 'pastor'
                $roleId = DB::table('roles')->where('name', 'pastor')->value('id');
                if ($roleId) {
                    DB::table('user_roles')->insertOrIgnore([
                        'user_id'    => $user->id,
                        'role_id'    => $roleId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Sinkronkan nama di akun user jika ada
                $user->update(['name' => $validated['nama']]);
            }
        } elseif ($validated['jabatan'] === 'uskup') {
            // Hapus akun jika sebelumnya pastor
            User::where('klerus_id', $klerus->id)->delete();
        }

        return redirect()
            ->route('sekretariat.klerus.index')
            ->with('success', 'Data klerus berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * Akun user terkait ikut dihapus.
     */
    public function destroy(Klerus $klerus)
    {
        User::where('klerus_id', $klerus->id)->delete();

        $klerus->delete();

        return redirect()
            ->route('sekretariat.klerus.index')
            ->with('success', 'Data klerus dan akun login terkait berhasil dihapus.');
    }
}