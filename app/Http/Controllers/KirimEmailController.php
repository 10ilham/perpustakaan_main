<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MailSendLandingPage;
use Illuminate\Support\Facades\Mail;

class KirimEmailController extends Controller
{
    public function index()
    {
        return view('layouts.emails.kontak');
    }

    public function kirim(Request $request)
    {
        $details = [
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'subject' => $request->subject,
            'komentar' => $request->komentar
        ];

        // Kirim email menggunakan MailSend
        Mail::to('munawar@pnc.ac.id')->send(new MailSendLandingPage($details));
        // Mail::to($request->email)->send(new MailSendLandingPage($details));

        // Redirect kembali dengan pesan sukses
        return back()->with('success', 'Pesan Anda telah berhasil dikirim!');
    }
}
