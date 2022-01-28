<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Partisipan;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255', 'email', 'unique:users'],
                'password' => ['required', 'string', new Password],
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Pendaftaran User Berhasil');
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Pendaftaran Gagal',
                500,
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                # code...
                return ResponseFormatter::error(
                    [
                        'message' => 'user tidak terdaftar',
                    ],
                    'Login Gagal',
                    500,
                );
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login Berhasil');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Login Gagal', 500);

            //throw $th;
        }
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success(
            $request->user(),
            'Data user berhasil Diambil'
        );
    }

    public function reset(Request $request)
    {
        try {
            //code...
            $request->validate([
                // 'email' => ['required', 'string', 'max:255', 'email', 'unique:users'],
                'password' => ['required', 'string', new Password],
            ]);
            $id = Auth::user()->id;
            $user = User::find($id);
            $user->password = Hash::make($request->password);
            $user->update();
            return ResponseFormatter::success(
                $user,
                'Berhasil Memperbaharui Password'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Gagal Memperbaharui Password',
                500,
            );
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            //code...
            $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'max:255', 'email'],
                'alamat' => ['string', 'max:255', 'nullable'],
                'medsos' => ['string', 'max:255', 'nullable'],
                'profile_photo_path' => ['nullable', 'image'],
                'deskripsi' => ['nullable'],
                'exp' => ['nullable', 'integer'],
            ]);
            // $data = $request->all();


            $user = Auth::user();
            if ($request->name) {
                $user->name = $request->name;
            }
            if ($request->email) {
                $user->email = $request->email;
            }
            if ($request->alamat) {
                $user->alamat = $request->alamat;
            }
            if ($request->medsos) {
                $user->medsos = $request->medsos;
            }
            if ($request->profile_photo_path) {
                $pathFile = $request->file('profile_photo_path')->store('public/user');
                $user->profile_photo_path = $pathFile;
            }
            if ($request->deskripsi) {
                $user->deskripsi = $request->deskripsi;
            }
            if ($request->exp) {
                $user->exp = $request->exp;
            }

            $user->update();
            // $user->update($data);

            return ResponseFormatter::success(
                $user,
                'Update Profile Berhasil'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Update Profile Gagal',
                500,
            );
        }
    }

    public function destroy()
    {
        $user = Auth::user();
        $id = $user->id;
        $user->delete();
        $partisipan = Partisipan::where([
            ['user_id', '=', $id],
        ]);
        $partisipan->delete();
        return ResponseFormatter::success(
            null,
            'User Berhasil Dihapus'
        );
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }

    public function getUsers(Request $request)
    {
        try {
            //code...
            $id = $request->input('id');
            if ($id) {
                // $konten = Konten::with(['user'])->find($id);
                $user = User::find($id);
                if ($user) {
                    return ResponseFormatter::success(
                        $user,
                        'Data User Berhasil Diambil'
                    );
                } else {
                    return ResponseFormatter::error(
                        null,
                        'Data User Tidak Ada'
                    );
                }
            }
            $user = User::all();
            return ResponseFormatter::success(
                $user,
                'Berhasil Menampilkan Data Seluruh User'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Gagal Menampilkan Data Seluruh User',
                500,
            );
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'max:255', 'email',],
                'alamat' => ['string', 'max:255', 'nullable'],
                'medsos' => ['string', 'max:255', 'nullable'],
                'deskripsi' => ['nullable'],
                'exp' => ['nullable', 'integer'],
                'level' => ['string', 'nullable', 'in:anggota,admin'],
                'active'  => ['nullable', 'integer'],
                'profile_photo_path' => ['nullable', 'image'],
                // 'level' => ['in:anggota,admin']
            ]);
            // $data = $request->all();
            $user = User::find($id);
            // $user->update($data);
            if ($request->name) {
                $user->name = $request->name;
            }
            if ($request->email) {
                $user->email = $request->email;
            }
            if ($request->alamat) {
                $user->alamat = $request->alamat;
            }
            if ($request->medsos) {
                $user->medsos = $request->medsos;
            }
            if ($request->profile_photo_path) {
                $pathFile = $request->file('profile_photo_path')->store('public/user');
                $user->profile_photo_path = $pathFile;
            }
            if ($request->deskripsi) {
                $user->deskripsi = $request->deskripsi;
            }
            if ($request->exp) {
                $user->exp = $request->exp;
            }
            if ($request->level) {
                $user->level = $request->level;
            }
            if ($request->active) {
                $user->active = $request->active;
            }

            $user->update();

            return ResponseFormatter::success(
                $user,
                'Update Data Akun User Berhasil'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                    'message' => 'something wrong',
                    'error' => $error
                ],
                'Update Data Akun User Gagal',
                500,
            );
        }
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);
        $user->delete();
        $partisipan = Partisipan::where([
            ['user_id', '=', $id],
        ]);
        $partisipan->delete();
        return ResponseFormatter::success(
            null,
            'User Berhasil Dihapus'
        );
    }
}
