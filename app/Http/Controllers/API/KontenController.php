<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Konten;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KontenController extends Controller
{
    //
    public function all(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            $jenis = $request->input('jenis');
            $status = $request->input('status');
            $limit = $request->input('limit');

            if ($id) {
                $konten = Konten::with(['user'])->find($id);
                if ($konten) {
                    return ResponseFormatter::success(
                        $konten,
                        'Data Konten Berhasil Diambil'
                    );
                } else {
                    return ResponseFormatter::error(
                        null,
                        'Data Konten Tidak Ada'
                    );
                }
            }

            $konten = Konten::with(['user']);
            // $konten = Konten::all();

            if ($jenis) {
                $konten->where('jenis', '=', $jenis);
            }
            if ($status) {
                $konten->where('status', '=', $status);
            }

            return ResponseFormatter::success(
                $konten->paginate($limit),
                'Data Konten Berhasil Diambil'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Menampilkan Konten', 500);
        }
    }

    public function test(Request $request)
    {
        // $jenis = $request->input('jenis');
        // $konten = DB::table('users')
        //     ->join('kontens', 'users.id', '=', 'kontens.user_id')
        //     ->select('kontens.*', 'users.name')
        //     ->get();
        // if ($jenis) {
        //     $konten->where('jenis', '=', $jenis);
        // }
        // return response()->json(['message' => 'Success', 'data' => $konten]);
        // return response()->json(['message' => 'Api TEst Success']);
            // test image
        try {
            //code...

            $pathFile = $request->file('gambar')->store('public/test');
            return response()->json(['message' => 'image Upload Success', 'path'=>$pathFile]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'image Upload Gagal']);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => ['required', 'string', 'max:255'],
                'gambar' => ['required', 'image'],
                'link' => ['nullable'],
                'deskripsi' => ['required'],
                'jenis' => ['required', 'in:artikel,video'],
                'status' => ['nullable', 'integer'],
            ]);

            $pathFile = $request->file('gambar')->store('public/konten');

            $konten = Konten::create([
                'judul' => $request->judul,
                'gambar' => $pathFile,
                'link' => $request->link,
                'deskripsi' => $request->deskripsi,
                'jenis' => $request->jenis,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
            ]);

            return ResponseFormatter::success(
                $konten,
                'Berhasil Menambahkan Konten Baru'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Gagal Menambahkan Konten Baru', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'judul' => ['nullable', 'string'],
                'gambar' => ['nullable'],
                'link' => ['nullable', 'url'],
                'deskripsi' => ['nullable'],
                'jenis' => ['nullable', 'in:artikel,video'],
                'status' => ['nullable', 'integer'],
            ]);

            // $data = $request->all();


            $konten = Konten::find($id);
            if ($request->judul) {
                $konten->judul = $request->judul;
            }
            if ($request->gambar) {
                $pathFile = $request->file('gambar')->store('public/konten');
                $konten->gambar = $pathFile;
            }
            if ($request->link) {
                $konten->link = $request->link;
            }
            if ($request->deskripsi) {
                $konten->deskripsi = $request->deskripsi;
            }
            if ($request->jenis) {
                $konten->jenis = $request->jenis;
            }
            if ($request->status) {
                $konten->status = $request->status;
            }
            $konten->update();

            return ResponseFormatter::success(
                $konten,
                'Update Data Akun Konten Berhasil'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Update Data Akun Konten Gagal',
                500,
            );
        }
    }

    public function delete(Request $request, $id)
    {
        $konten = Konten::find($id);
        $konten->delete();
        return ResponseFormatter::success(
            null,
            'Konten Berhasil Dihapus'
        );
    }
}
