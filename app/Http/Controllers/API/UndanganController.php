<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Partisipan;
use App\Models\Undangan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UndanganController extends Controller
{
    //
    public function all(Request $request)
    {
        try {
            $id = $request->input('id');
            $status = $request->input('status');
            $user_id = $request->input('user');
            $limit = $request->input('limit');

            if ($id) {
                $undangan = Undangan::with(['user'])->find($id);
                if ($undangan) {
                    return ResponseFormatter::success(
                        $undangan,
                        'Data Undangan Berhasil Diambil'
                    );
                } else {
                    return ResponseFormatter::error(
                        null,
                        'Data Undangan Tidak Ada'
                    );
                }
            }
            if ($user_id) {
                $undangan = Undangan::with(['user'])->where('user_id', $user_id);
                if ($undangan) {
                    return ResponseFormatter::success(
                        $undangan->paginate($limit),
                        'Data Undangan Berhasil Diambil'
                    );
                } else {
                    return ResponseFormatter::error(
                        null,
                        'Data Undangan Tidak Ada'
                    );
                }
            }

            $undangan = undangan::with(['user']);
            if ($status) {
                $undangan->where('status', '=', $status);
            }

            return ResponseFormatter::success(
                $undangan->paginate($limit),
                'Data Undangan Berhasil Diambil '
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Menampilkan Undangan', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'pengirim' => ['required', 'string'],
                'nm_kegiatan' => ['required', 'string'],
                'lokasi' => ['required', 'string'],
                'tgl' => ['required', 'date'],
                'deskripsi' => ['required', 'string'],
                'jenis' => ['required', 'in:baksos,sekolah,korporat'],
                'penyelenggara' => ['required', 'string'],
                'contact' => ['required', 'string'],
                'status' => ['nullable', 'in:tunggu,terima,tolak'],
            ]);

            $undangan = Undangan::create([
                'user_id' => Auth::user()->id,
                'pengirim' => $request->pengirim,
                'nm_kegiatan' => $request->nm_kegiatan,
                'lokasi'  => $request->lokasi,
                'tgl' => $request->tgl,
                'deskripsi' => $request->deskripsi,
                'jenis' => $request->jenis,
                'penyelenggara' => $request->penyelenggara,
                'contact' => $request->contact,
                'status' => $request->status ? $request->status : 'tunggu'
            ]);

            return ResponseFormatter::success(
                $undangan,
                'Berhasil Menambahkan Undangan Baru'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Menambahkan Undangan Baru', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'pengirim' => ['nullable', 'string'],
                'nm_kegiatan' => ['nullable', 'string'],
                'lokasi' => ['nullable', 'string'],
                'tgl' => ['nullable', 'date'],
                'deskripsi' => ['nullable', 'string'],
                'jenis' => ['nullable', 'in:baksos,sekolah,korporat'],
                'penyelenggara' => ['nullable', 'string'],
                'contact' => ['nullable', 'string'],
                'status' => ['nullable', 'in:tunggu,terima,tolak'],
            ]);

            $mendongeng = Undangan::find($id);
            if ($request->pengirim) {
                $mendongeng->pengirim = $request->pengirim;
            }
            if ($request->nm_kegiatan) {
                $mendongeng->nm_kegiatan = $request->nm_kegiatan;
            }
            if ($request->lokasi) {
                $mendongeng->lokasi = $request->lokasi;
            }
            if ($request->tgl) {
                $mendongeng->tgl = $request->tgl;
            }
            if ($request->deskripsi) {
                $mendongeng->deskripsi = $request->deskripsi;
            }
            if ($request->jenis) {
                $mendongeng->jenis = $request->jenis;
            }
            if ($request->penyelenggara) {
                $mendongeng->penyelenggara = $request->penyelenggara;
            }
            if ($request->contact) {
                $mendongeng->contact = $request->contact;
            }
            if ($request->status) {
                $mendongeng->status = $request->status;
            }
            $mendongeng->update();

            return ResponseFormatter::success(
                $mendongeng,
                'Update Data Undangan Berhasil'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Update Data Undangan Gagal',
                500,
            );
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            //code...
            $mendongeng = Undangan::find($id);
            $mendongeng->delete();
            $partisipan = Partisipan::where([
                ['mendongeng_id', '=', $id],
            ]);
            $partisipan->delete();

            return ResponseFormatter::success(
                null,
                'Undangan Berhasil Dihapus'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Undangan Gagal Dihapus',
                500,
            );
        }
    }
}
