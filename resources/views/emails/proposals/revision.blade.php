@extends('emails.layouts.app')

@section('content')
    <h1 style="color: #2d3748; font-size: 24px; font-weight: bold; margin-bottom: 20px;">Revisi Proposal</h1>

    <p style="color: #4a5568; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        Halo <strong>{{ $proposal->pic_name }}</strong>,
    </p>

    <p style="color: #4a5568; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        Proposal kegiatan Anda dengan judul <strong>"{{ $proposal->nama_kegiatan }}"</strong> memerlukan revisi.
    </p>

    <div style="background-color: #f7fafc; border-left: 4px solid #4299e1; padding: 15px; margin: 20px 0;">
        <h2 style="color: #2d3748; font-size: 18px; margin-bottom: 10px;">Catatan Revisi:</h2>
        <p style="color: #4a5568; font-size: 16px; line-height: 1.5; margin: 0;">
            {{ $proposal->revision_note }}
        </p>
    </div>

    <p style="color: #4a5568; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">
        Silakan login ke sistem untuk melihat detail revisi dan melakukan perbaikan yang diperlukan.
    </p>

    <table border="0" cellpadding="0" cellspacing="0"
        style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
        <tr>
            <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                <table border="0" cellpadding="0" cellspacing="0"
                    style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                    <tr>
                        <td
                            style="font-family: sans-serif; font-size: 14px; vertical-align: top; border-radius: 5px; text-align: center; background-color: #4299e1;">
                            <a href="{{ route('proposals.show', $proposal->id) }}" target="_blank"
                                style="display: inline-block; color: #ffffff; background-color: #4299e1; border: solid 1px #4299e1; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 16px; font-weight: bold; margin: 0; padding: 12px 24px; text-transform: capitalize; border-color: #4299e1;">
                                Lihat Detail Proposal
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="color: #4a5568; font-size: 16px; line-height: 1.5; margin-top: 20px;">
        Terima kasih,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
@endsection
