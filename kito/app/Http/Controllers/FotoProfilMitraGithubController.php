<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class FotoProfilMitraGithubController extends Controller
{
    public function getProfileImage($sobat_id)
    {
        // Konfigurasi dari file .env (tidak ada perubahan)
        $githubToken = env('GITHUB_TOKEN');
        $githubUser = env('GITHUB_USERNAME');
        $githubRepo = env('GITHUB_REPO');
        $branch = 'main';
        $imageFolder = 'fotoProfilMitra';
        $imageName = $sobat_id . '.jpg';

        if (!$githubToken || !$githubUser || !$githubRepo) {
            return response('Server configuration error.', 500);
        }

        $githubUrl = "https://raw.githubusercontent.com/{$githubUser}/{$githubRepo}/{$branch}/{$imageFolder}/{$imageName}";

        // Mengambil gambar dari GitHub (tidak ada perubahan)
        $response = Http::withToken($githubToken)
            ->withHeaders(['User-Agent' => 'Laravel-Image-Proxy'])
            ->get($githubUrl);

        // Jika gambar berhasil didapat dari GitHub
        if ($response->successful()) {
            // Kirim gambar ke browser dengan Content-Type yang benar
            return Response::make($response->body(), 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Length' => strlen($response->body())
            ]);
        }

        // #################### PENYESUAIAN DI SINI ####################
        // Jika gambar tidak ditemukan di GitHub (atau error lain),
        // cukup kirim response 404 Not Found.
        // Browser akan menangkap error ini dan menjalankan event 'onerror' pada tag <img>.
        return response('Image not found on GitHub.', 404);
        // ###########################################################
    }
}
