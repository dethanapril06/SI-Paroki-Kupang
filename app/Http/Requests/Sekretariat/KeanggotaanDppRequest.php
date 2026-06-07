<?php

namespace App\Http\Requests\Sekretariat;

use Illuminate\Foundation\Http\FormRequest;

class KeanggotaanDppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_umat'      => ['required', 'integer', 'exists:umat,id'],
            'jabatan'      => ['required', 'in:Ketua,Wakil Ketua,Sekretaris,Bendahara,Koordinator Bidang,Anggota,Lainnya'],
            'bidang_tugas' => ['nullable', 'string', 'max:50'],
            'status_aktif' => ['required', 'in:Aktif,Nonaktif'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_umat.required'      => 'Umat wajib dipilih.',
            'id_umat.exists'        => 'Umat yang dipilih tidak ditemukan.',
            'jabatan.required'      => 'Jabatan wajib dipilih.',
            'jabatan.in'            => 'Jabatan tidak valid.',
            'bidang_tugas.max'      => 'Bidang tugas maksimal 50 karakter.',
            'status_aktif.required' => 'Status aktif wajib dipilih.',
            'status_aktif.in'       => 'Status hanya boleh Aktif atau Nonaktif.',
        ];
    }
}