@extends('emails.layouts.app')

@section('content')
    <!-- Logo Header -->
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" style="max-width: 200px; height: auto;">
    </div>

    <!-- Judul -->
    <h1
        style="color: #2d3748; font-size: 28px; font-weight: bold; margin-bottom: 25px; text-align: center; font-family: 'Arial', sans-serif;">
        Revisi Proposal
    </h1>

    <!-- Salam -->
    <div
        style="background-color: #ffffff; border-radius: 10px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin-bottom: 15px;">
            Halo <strong style="color: #2d3748;">{{ $proposal->pic_name }}</strong>,
        </p>

        <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
            Proposal kegiatan Anda dengan judul <strong style="color: #2d3748;">"{{ $proposal->nama_kegiatan }}"</strong>
            memerlukan revisi.
        </p>
    </div>

    <!-- Catatan Revisi -->
    <div
        style="background-color: #f8fafc; border-left: 4px solid #4299e1; padding: 20px; margin: 25px 0; border-radius: 0 8px 8px 0;">
        <h2 style="color: #2d3748; font-size: 20px; margin-bottom: 15px; font-family: 'Arial', sans-serif;">
            Catatan Revisi:
        </h2>
        <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin: 0; white-space: pre-wrap;">
            {{ $proposal->revision_note }}
        </p>

        <!-- Instruksi -->
        <div
            style="background-color: #ffffff; border-radius: 10px; padding: 25px; margin: 25px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                Silakan login ke sistem untuk melihat detail revisi dan melakukan perbaikan yang diperlukan.
            </p>

            <!-- Tombol -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('proposals.show', $proposal->id) }}"
                    style="display: inline-block; 
                  background-color: #4299e1; 
                  color: #ffffff; 
                  text-decoration: none; 
                  padding: 15px 30px; 
                  border-radius: 8px; 
                  font-size: 16px; 
                  font-weight: bold; 
                  transition: background-color 0.3s ease;">
                    Lihat Detail Proposal
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin: 0;">
                Terima kasih,<br>
                <strong style="color: #2d3748;">{{ config('app.name') }}</strong>
            </p>
        </div>
    </div>
@endsection
