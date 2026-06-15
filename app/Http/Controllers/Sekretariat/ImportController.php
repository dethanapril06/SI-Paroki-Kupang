<?php

namespace App\Http\Controllers\Sekretariat;

use App\Exports\Templates\BaptisTemplateExport;
use App\Exports\Templates\KeluargaTemplateExport;
use App\Exports\Templates\KomuniPertamaTemplateExport;
use App\Exports\Templates\KrismaTemplateExport;
use App\Exports\Templates\KubTemplateExport;
use App\Exports\Templates\MinyakSuciTemplateExport;
use App\Exports\Templates\PernikahanTemplateExport;
use App\Exports\Templates\UmatTemplateExport;
use App\Exports\Templates\WilayahTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\BaptisImport;
use App\Imports\KeluargaImport;
use App\Imports\KomuniPertamaImport;
use App\Imports\KrismaImport;
use App\Imports\KubImport;
use App\Imports\MinyakSuciImport;
use App\Imports\PernikahanImport;
use App\Imports\UmatImport;
use App\Imports\WilayahImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ImportController extends Controller
{
    /**
     * Daftar jenis import yang didukung.
     */
    private const JENIS = [
        'wilayah'         => ['label' => 'Wilayah',          'icon' => 'bi-map',           'urutan' => 1],
        'kub'             => ['label' => 'KUB',               'icon' => 'bi-people-fill',   'urutan' => 2],
        'keluarga'        => ['label' => 'Keluarga',          'icon' => 'bi-house-fill',    'urutan' => 3],
        'umat'            => ['label' => 'Umat',              'icon' => 'bi-person-fill',   'urutan' => 4],
        'baptis'          => ['label' => 'Baptis',            'icon' => 'bi-droplet-fill',  'urutan' => 5],
        'komuni-pertama'  => ['label' => 'Komuni Pertama',    'icon' => 'bi-cup-fill',      'urutan' => 6],
        'krisma'          => ['label' => 'Krisma',            'icon' => 'bi-star-fill',     'urutan' => 7],
        'pernikahan'      => ['label' => 'Pernikahan',        'icon' => 'bi-heart-fill',    'urutan' => 8],
        'minyak-suci'     => ['label' => 'Minyak Suci',       'icon' => 'bi-droplet-half',  'urutan' => 9],
    ];

    public function index(): View
    {
        return view('sekretariat.import.index', [
            'jenisImport' => self::JENIS,
        ]);
    }

    /**
     * Download template Excel untuk jenis data tertentu.
     */
    public function downloadTemplate(string $jenis): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $map = [
            'wilayah'        => [WilayahTemplateExport::class,       'template_wilayah.xlsx'],
            'kub'            => [KubTemplateExport::class,            'template_kub.xlsx'],
            'keluarga'       => [KeluargaTemplateExport::class,       'template_keluarga.xlsx'],
            'umat'           => [UmatTemplateExport::class,           'template_umat.xlsx'],
            'baptis'         => [BaptisTemplateExport::class,         'template_baptis.xlsx'],
            'komuni-pertama' => [KomuniPertamaTemplateExport::class,  'template_komuni_pertama.xlsx'],
            'krisma'         => [KrismaTemplateExport::class,         'template_krisma.xlsx'],
            'pernikahan'     => [PernikahanTemplateExport::class,     'template_pernikahan.xlsx'],
            'minyak-suci'    => [MinyakSuciTemplateExport::class,     'template_minyak_suci.xlsx'],
        ];

        if (!isset($map[$jenis])) {
            abort(404, 'Jenis template tidak ditemukan.');
        }

        [$exportClass, $filename] = $map[$jenis];

        return Excel::download(new $exportClass(), $filename);
    }

    /**
     * Proses upload dan import file Excel.
     */
    public function import(Request $request, string $jenis): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $importMap = [
            'wilayah'        => WilayahImport::class,
            'kub'            => KubImport::class,
            'keluarga'       => KeluargaImport::class,
            'umat'           => UmatImport::class,
            'baptis'         => BaptisImport::class,
            'komuni-pertama' => KomuniPertamaImport::class,
            'krisma'         => KrismaImport::class,
            'pernikahan'     => PernikahanImport::class,
            'minyak-suci'    => MinyakSuciImport::class,
        ];

        if (!isset($importMap[$jenis])) {
            abort(404, 'Jenis import tidak ditemukan.');
        }

        $importClass = $importMap[$jenis];
        $label       = self::JENIS[$jenis]['label'] ?? $jenis;

        try {
            Excel::import(new $importClass(), $request->file('file'));

            return redirect()
                ->route('sekretariat.import.index')
                ->with('success', "Import data {$label} berhasil!");

        } catch (ValidationException $e) {
            // Validasi kolom (dari WithValidation)
            $messages = collect($e->failures())
                ->map(fn($f) => "Baris {$f->row()}, kolom \"{$f->attribute()}\": " . implode(', ', $f->errors()))
                ->take(20)
                ->all();

            return redirect()
                ->route('sekretariat.import.index')
                ->with('import_errors', $messages)
                ->with('import_jenis', $jenis)
                ->with('error', "Import data {$label} gagal karena ada kesalahan validasi. Silakan periksa file Anda.");

        } catch (\Exception $e) {
            // Error business logic (duplikat, data tidak ditemukan, dll.)
            return redirect()
                ->route('sekretariat.import.index')
                ->with('import_errors', [$e->getMessage()])
                ->with('import_jenis', $jenis)
                ->with('error', "Import data {$label} dihentikan karena terjadi kesalahan.");
        }
    }
}
