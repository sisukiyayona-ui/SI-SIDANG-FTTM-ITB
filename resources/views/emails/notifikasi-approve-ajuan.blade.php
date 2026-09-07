<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #e3e6ea;">
                    <tr>
                        <td style="background-color:#6998d3; color:#ffffff; padding:20px 28px;">
                            <h2 style="margin:0; font-size:18px;">Permohonan Approve Ajuan Sidang</h2>
                            <p style="margin:4px 0 0; font-size:13px;">SI SIDANG FTTM ITB</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 12px; font-size:14px; color:#333333;">Yth. Bapak/Ibu {{ $data['nama_kpps'] }},</p>
                            <p style="margin:0 0 16px; font-size:14px; color:#333333; line-height:1.6;">
                                Dengan ini kami informasikan bahwa terdapat ajuan sidang yang membutuhkan persetujuan Anda sebagai anggota KPPS.
                                Mohon melakukan review dan approve ajuan berikut:
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e3e6ea; border-collapse:collapse; font-size:14px; color:#333333;">
                                <tr>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e3e6ea; background-color:#f8f9fb; width:35%;"><strong>Nama Mahasiswa</strong></td>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e3e6ea;">{{ $data['nama_mhs'] }} ({{ $data['nim'] }})</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e3e6ea; background-color:#f8f9fb;"><strong>Judul</strong></td>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e3e6ea;">{{ $data['judul'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e3e6ea; background-color:#f8f9fb;"><strong>Tahapan Sidang</strong></td>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e3e6ea;">{{ $data['tahapan'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e3e6ea; background-color:#f8f9fb;"><strong>Strata</strong></td>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e3e6ea;">{{ $data['strata'] }}</td>
                                </tr>
                            </table>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $data['approve_url'] }}" style="display:inline-block; background-color:#6998d3; color:#ffffff; text-decoration:none; padding:12px 32px; border-radius:6px; font-size:15px; font-weight:bold;">Approve Sekarang</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:20px 0 0; font-size:12px; color:#888888; line-height:1.6;">
                                Email ini dikirim oleh sistem SI SIDANG FTTM ITB atas permohonan TU Prodi/FS.
                                Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
