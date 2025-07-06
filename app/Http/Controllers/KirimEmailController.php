<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MailSendLandingPage;
use Illuminate\Support\Facades\Mail;

class KirimEmailController extends Controller
{
    /**
     * Tampilkan halaman kontak
     */
    public function index()
    {
        return view('layouts.emails.kontak');
    }

    /**
     * Kirim email dari form kontak
     */
    public function kirim(Request $request)
    {
        // Siapkan data email
        $details = [
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'subject' => $request->subject,
            'komentar' => $request->komentar
        ];

        // Kirim email ke admin
        Mail::to(env('MAIL_FROM_ADDRESS'))->send(new MailSendLandingPage($details));

        return back()->with('success', 'Pesan Anda telah berhasil dikirim!');
    }
}
