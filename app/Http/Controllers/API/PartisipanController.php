<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Mendongeng;
use App\Models\Partisipan;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

// use Illuminate\support\Facades\Gate

class PartisipanController extends Controller
{
    //
    public function all(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $limit = $request->input('limit');

            if ($id) {
                $partisipan = Partisipan::with(['user', 'mendongeng'])->find($id);
                if ($partisipan) {
                    return ResponseFormatter::success(
                        $partisipan,
                        'Data Partisipan Berhasil Diambil'
                    );
                } else {
                    return ResponseFormatter::error(
                        null,
                        'Data Partisipan Tidak Ada'
                    );
                }
            }
            $partisipan = Partisipan::with(['user', 'mendongeng']);

            return ResponseFormatter::success(
                $partisipan->paginate($limit),
                'Berhasil Menampilkan Partisipan'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Menampilkan Partisipan', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            //code...
            $request->validate([
                'peran' => ['required'],
                'mendongeng_id' => ['required', 'integer'],
                'st_req' => ['nullable', 'integer'],
            ]);
            $checkPartisipan = Partisipan::where([
                ['user_id', '=', Auth::user()->id],
                ['mendongeng_id', '=', $request->mendongeng_id],
            ])->get();

            if (!count($checkPartisipan) == 0) {
                return ResponseFormatter::error(
                    null,
                    'User sudah terdata'
                );
            }
            if ($request->peran == 'pendongeng') {
                # code...
                $checkJumlahPendongeng = Partisipan::where([
                    ['peran', '=', $request->peran],
                    ['mendongeng_id', '=', $request->mendongeng_id],
                ])->get();
                if (count($checkJumlahPendongeng) >= $request->st_req) {
                    # code...
                    return ResponseFormatter::error(
                        null,
                        'Jumlah Pendongeng Sudah Penuh'
                    );
                }
            }
            $partisipan = Partisipan::create([
                'user_id' => Auth::user()->id,
                'mendongeng_id' => $request->mendongeng_id,
                'peran' => $request->peran,
            ]);
            return ResponseFormatter::success(
                $partisipan,
                'Berhasil Mendata Partisipan Baru'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Mendata Partisipan Baru', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'peran' => ['required'],
            ]);
            $data = $request->all();
            $partisipan = Partisipan::with(['user', 'mendongeng'])->find($id);
            $partisipan->update($data);

            return ResponseFormatter::success(
                $partisipan,
                'Berhasil Mengubah Peran Partisipan'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Mengubah Peran Partisipan', 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $partisipan = Partisipan::find($id);
        $partisipan->delete();
        return ResponseFormatter::success(
            null,
            'Partisipa Berhasil Dihapus'
        );
    }

    public function test(Request $request)
    {

        // $checkPartisipan = Partisipan::where([
        //     ['user_id', '=', Auth::user()->id],
        //     ['mendongeng_id', '=', $request->mendongeng_id],
        // ])->get();

        // if (!count($checkPartisipan) == 0) {
        //     return ResponseFormatter::error(
        //         null,
        //         'User sudah terdata'
        //     );
        // }

        // return response()->json(['pesan' => 'berhasil', 'data' => count($checkPartisipan) == 0]);
        // -----------------------------------------------------------------------------
        try {
            //code...
            // $partisipan = Partisipan::where([
            //     ['user_id', '=', Auth::user()->id],
            // ]);
            // $partisipan->delete();
            // ------------------------

            $request->validate([
                'peran' => ['required'],
                'mendongeng_id' => ['required', 'integer'],
                'st_req' => ['nullable', 'integer'],
            ]);
            // $checkPartisipan = Partisipan::where([
            //     ['peran', '=', $request->peran],
            //     ['mendongeng_id', '=', $request->mendongeng_id],
            // ])->get();
            if ($request->peran == 'pendongeng') {
                # code...
                $checkJumlahPendongeng = Partisipan::where([
                    ['peran', '=', $request->peran],
                    ['mendongeng_id', '=', $request->mendongeng_id],
                ])->get();
                if (count($checkJumlahPendongeng) >= $request->st_req) {
                    # code...
                    return ResponseFormatter::error(
                        null,
                        'Jumlah Pendongeng Sudah Penuh'
                    );
                }
            }
            return response()->json(['pesan' => 'berhasil', 'data' => 'mendongeng id: ' . $request->mendongeng_id . ' | peran: ' . $request->peran . ' | jumlah pendongeng: ' . $request->st_req . ' | jumlah saat ini: ' . count($checkJumlahPendongeng)]);


            // return response()->json(['pesan' => 'berhasil', 'data' => $partisipan->paginate(10)]);

        } catch (Exception $error) {
            //throw $th;
            // return response()->json(['pesan' => 'gagal', 'data' => $partisipan->paginate(10), 'error' => $error]);
            return response()->json(['pesan' => 'gagal', 'err' => $error]);
        }
    }

    public function test2(Request $request)
    {
        try {
            //code...
            // test get
            // $response = Http::get('http://kom-mendongeng-api.test/api/undangans');
            // $response = Http::withHeaders([
            //     'Authorization' => 'Bearer 46|Y282jOu2V1ZxIhLMKAXbd5H3WpaPU6uf0SxfcllK'
            // ])->get('http://kom-mendongeng-api.test/api/user');

            // test post
            // $response = Http::post('http://kom-mendongeng-api.test/api/ptest3', [
            //     'peran' => 'pendongeng',
            //     'mendongeng_id' => '4',
            //     'st_req' => '4',
            // ]);

            // test fcm
            $response = Http::withHeaders([
                'Authorization' => 'key=AAAAwBf21ds:APA91bE1aXaygXKQlXnNSl0kFC_FetRdKdiupCR3wO1nmSEy3Lq3mzfz2xEpdhrBh8csQrWBkmEhsTNnVWWBXVTd4Sj_b7bbGXX9KkblRYUmJnNoc5xzwAHPp1jvXATZmzu-qkoXjBIs',
                'Content-Type' => 'application/json'
            ])->post('https://fcm.googleapis.com/fcm/send', [
                "to" => "/topics/mendongeng",
                "collapse_key" => "type_a",
                "notification" => [
                    "body" => "Body Send From Laravel",
                    "title" => "Test Send Notif with Http",
                    "android_channel_id" => "high_importance_channel"
                ],
                // "data" => [
                //     "route" => "red"
                // ]
            ]);
            if ($response->status() == 200) {
                # code...
                return response()->json(['pesan' => 'berhasil ok', 'data' => $response->body()]);
                // return response()->json(['pesan' => 'berhasil ok', 'data' => $response->status()]);
            } else {
                # code...
                return response()->json(['pesan' => 'gagal ok', 'data' => $response->status()]);
            }
            // return response()->json(['pesan' => 'berhasil ok', 'data' => $response->status()]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['pesan' => 'gagal ex', 'data' => $th]);
        }
    }

    public function test3(Request $request)
    {
        try {
            //code...
            // String  = "konten baru";
            // $response = Http::withHeaders([
            //     'Authorization' => 'key=AAAAwBf21ds:APA91bE1aXaygXKQlXnNSl0kFC_FetRdKdiupCR3wO1nmSEy3Lq3mzfz2xEpdhrBh8csQrWBkmEhsTNnVWWBXVTd4Sj_b7bbGXX9KkblRYUmJnNoc5xzwAHPp1jvXATZmzu-qkoXjBIs',
            //     'Content-Type' => 'application/json'
            // ])->post('https://fcm.googleapis.com/fcm/send', [
            //     "to" => "/topics/konten",
            //     "collapse_key" => "type_a",
            //     "notification" => [
            //         // "body" => "Konten Baru - " + $request->judul,
            //         "body" => "Konten Baru - ",
            //         "title" => "Komunitas Bali Mendongeng"

            //     ],

            // ]);

            $date = Carbon::tomorrow()->toDateString();
            $mendongeng = Mendongeng::where([
                ['tgl', '=', '2022-03-22'],
            ])->first();
            
            // $mendongeng = Mendongeng::find('1');
            // $s = $mendongeng->name;

            // return response()->json(['pesan' => 'berhasil', 'data' =>  $response->body() . ' | ' . $request->a]);
            // return response()->json(['schedule'=>$date.' Mendongeng']);
            // return response()->json(['schedule' => $s]);
            return response()->json(['schedule' => $mendongeng->name]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['pesan' => 'gagal', 'data' => $th]);
        }
    }
}
