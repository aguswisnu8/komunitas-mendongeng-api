<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Mendongeng;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;

class MendongengController extends Controller
{
    //
    public function all(Request $request)
    {
        try {
            $id = $request->input('id');
            $tgl = $request->input('tgl');
            $status = $request->input('status');
            $limit = $request->input('limit');

            if ($id) {
                $mendongeng = Mendongeng::with(['undangan'])->find($id);
                if ($mendongeng) {
                    return ResponseFormatter::success(
                        $mendongeng,
                        'Data Kegiatan Mendongeng Berhasil Diambil'
                    );
                } else {
                    return ResponseFormatter::error(
                        null,
                        'Data Kegiatan Mendongeng Tidak Ada'
                    );
                }
            }
            // $date = Date::now();
            $date = Carbon::now()->toDateString();

            $mendongeng = Mendongeng::with(['undangan']);
            if ($tgl) {
                $mendongeng->where('tgl', '>=', $date);
            }
            if ($status) {
                $mendongeng->where('status', '=', $status);
            }

            return ResponseFormatter::success(
                $mendongeng->paginate($limit),
                'Data Kegiatan Mendongeng Berhasil Diambil '
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Menampilkan Kegiatan Mendogeng', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string'],
                'lokasi' => ['required', 'string'],
                'tgl' => ['required', 'date'],
                'deskripsi' => ['required', 'string'],
                'gambar' => ['nullable', 'image'],
                'partner' => ['required'],
                'jenis' => ['required', 'in:baksos,sekolah,korporat'],
                'status' => ['nullable', 'integer'],
                'gmap_link' => ['nullable'],
                'udangan_id' => ['nullable'],
                'exp_req' => ['required', 'integer'],
                'st_req' => ['required', 'integer'],
            ]);

            $pathFile = $request->file('gambar')->store('public/mendongeng');

            $mendongeng = Mendongeng::create([
                'name' => $request->name,
                'lokasi'  => $request->lokasi,
                'tgl' => $request->tgl,
                'deskripsi' => $request->deskripsi,
                'gambar' => $pathFile,
                'partner' => $request->partner,
                'jenis' => $request->jenis,
                'status' => $request->status ? $request->status : 1,
                'gmap_link' => $request->gmap_link,
                'udangan_id' => $request->udangan_id,
                'exp_req' => $request->exp_req,
                'st_req' => $request->st_req,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'key=AAAAwBf21ds:APA91bE1aXaygXKQlXnNSl0kFC_FetRdKdiupCR3wO1nmSEy3Lq3mzfz2xEpdhrBh8csQrWBkmEhsTNnVWWBXVTd4Sj_b7bbGXX9KkblRYUmJnNoc5xzwAHPp1jvXATZmzu-qkoXjBIs',
                'Content-Type' => 'application/json'
            ])->post('https://fcm.googleapis.com/fcm/send', [
                "to" => "/topics/mendongeng",
                "collapse_key" => "type_a",
                "notification" => [
                    "body" => $request->tgl . ' - ' .  $request->name,
                    "title" => "Kegiatan Mendongeng Baru - Bali Mendongeng",
                    "android_channel_id" => "high_importance_channel"
                ],
            ]);



            return ResponseFormatter::success(
                $mendongeng,
                'Berhasil Menambahkan Kegiatan Mendongeng Baru' . ' Notif ' . $response->status()
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Menambahkan Kegiatan Mendongeng Baru', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => ['nullable', 'string'],
                'lokasi' => ['nullable', 'string'],
                'tgl' => ['nullable', 'date'],
                'deskripsi' => ['nullable', 'string'],
                'gambar' => ['nullable'],
                'partner' => ['nullable'],
                'jenis' => ['nullable', 'in:baksos,sekolah,korporat'],
                'status' => ['nullable', 'integer'],
                'gmap_link' => ['nullable'],
                'udangan_id' => ['nullable'],
                'exp_req' => ['nullable', 'integer'],
                'st_req' => ['nullable', 'integer'],
            ]);

            $mendongeng = Mendongeng::find($id);
            if ($request->name) {
                $mendongeng->name = $request->name;
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
            if ($request->gambar) {
                $pathFile = $request->file('gambar')->store('public/mendongeng');
                $mendongeng->gambar = $pathFile;
            }
            if ($request->partner) {
                $mendongeng->partner = $request->partner;
            }
            if ($request->jenis) {
                $mendongeng->jenis = $request->jenis;
            }
            if ($request->status) {
                $mendongeng->status = $request->status;
            }
            if ($request->gmap_link) {
                $mendongeng->gmap_link = $request->gmap_link;
            }
            if ($request->udangan_id) {
                $mendongeng->udangan_id = $request->udangan_id;
            }
            if ($request->exp_req) {
                $mendongeng->exp_req = $request->exp_req;
            }
            if ($request->st_req) {
                $mendongeng->st_req = $request->st_req;
            }
            $mendongeng->update();

            $response = Http::withHeaders([
                'Authorization' => 'key=AAAAwBf21ds:APA91bE1aXaygXKQlXnNSl0kFC_FetRdKdiupCR3wO1nmSEy3Lq3mzfz2xEpdhrBh8csQrWBkmEhsTNnVWWBXVTd4Sj_b7bbGXX9KkblRYUmJnNoc5xzwAHPp1jvXATZmzu-qkoXjBIs',
                'Content-Type' => 'application/json'
            ])->post('https://fcm.googleapis.com/fcm/send', [
                "to" => "/topics/mendongeng",
                "collapse_key" => "type_a",
                "notification" => [
                    "body" => $mendongeng->tgl . ' - ' .  $mendongeng->name,
                    "title" => "Update Kegiatan",
                    "android_channel_id" => "high_importance_channel"
                ],
            ]);

            return ResponseFormatter::success(
                $mendongeng,
                'Update Data Kegiatan Mendongeng Berhasil' . ' Notif ' . $response->status()
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Update Data Kegiatan Mendongeng Gagal',
                500,
            );
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            //code...
            $mendongeng = Mendongeng::find($id);
            $mendongeng->delete();
            return ResponseFormatter::success(
                null,
                'Kegiatan Mendongeng Berhasil Dihapus'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Kegiatan Mendongeng Gagal Dihapus',
                500,
            );
        }
    }

    public function scheduleTest()
    {
        // $date = Carbon::now()->toDateString();
        $date = Carbon::tomorrow()->toDateString();
        $mendongeng = Mendongeng::where([
            ['tgl', '=', $date],
        ])->first();
        if ($mendongeng == '') {
            # code...
            info('test schedule from controller -' . $date . ' Mendongeng = Kosong');
        } else {
            # code...
            $response = Http::withHeaders([
                'Authorization' => 'key=AAAAwBf21ds:APA91bE1aXaygXKQlXnNSl0kFC_FetRdKdiupCR3wO1nmSEy3Lq3mzfz2xEpdhrBh8csQrWBkmEhsTNnVWWBXVTd4Sj_b7bbGXX9KkblRYUmJnNoc5xzwAHPp1jvXATZmzu-qkoXjBIs',
                'Content-Type' => 'application/json'
            ])->post('https://fcm.googleapis.com/fcm/send', [
                "to" => "/topics/mendongeng",
                "collapse_key" => "type_a",
                "notification" => [
                    "body" => $mendongeng->tgl . ' - ' .  $mendongeng->name,
                    "title" => "Reminder Mendongeng Keliling",
                    "android_channel_id" => "high_importance_channel"
                ],
            ]);

            // info('test schedule from controller -' . $date . ' Mendongeng = ' . $mendongeng);
            info('test schedule from controller -' . $date . ' Notif Status = ' . $response->status());
        }
    }
}
