<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Partisipan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                'mendongeng_id' => ['required', 'integer']
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


        try {
            //code...
            $partisipan = Partisipan::where([
                ['user_id', '=', Auth::user()->id],
            ]);
            $partisipan->delete();
            return response()->json(['pesan' => 'berhasil', 'data' => $partisipan->paginate(10)]);
        } catch (Exception $error) {
            //throw $th;
            return response()->json(['pesan' => 'gagal', 'data' => $partisipan->paginate(10), 'error' => $error]);
        }
    }
}
