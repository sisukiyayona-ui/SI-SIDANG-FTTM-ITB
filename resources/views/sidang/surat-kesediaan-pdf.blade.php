<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 45px 55px; }
        body {
            font-family: "DejaVu Serif";
            font-size: 12px;
            color: #000;
            line-height: 1.4;
        }
        .letterhead { text-align: center; font-weight: bold; }
        .letterhead .brand { font-size: 16px; }
        .letterhead .fakultas { font-size: 13px; }
        .letterhead .alamat { font-size: 10px; font-weight: normal; }
        .rule { border-bottom: 2px solid #000; margin: 6px 0 0; }
        .field { margin: 14px 0 0; }
        .row { margin: 8px 0; }
        .label { display: inline-block; width: 105px; vertical-align: top; position: absolute; left: 0; }
        .value { margin-left: 105px; }
        .rel { position: relative; }
        .normal { font-weight: normal; }
        .indent { padding-left: 105px; }
        .mt { margin-top: 12px; }
        .justify { text-align: justify; }
        .ttd { margin-top: 40px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="letterhead">
        <div class="brand">INSTITUT TEKNOLOGI BANDUNG</div>
        <div class="sub">FAKULTAS TEKNIK PERTAMBANGAN DAN PERMINYAKAN</div>
        <div class="alamat">
            Gedung Basic Science Center B Lantai 4, Jalan Ganesa 10 Bandung 40132, Telp.: +6222 2506282<br>
            Fax.: +6222 2514922, E-mail: info@fttm.itb.ac.id, http://www.fttm.itb.ac.id
        </div>
    </div>
    <div class="rule"></div>

    <div class="field">
        <div class="row"><span class="label bold">Nomor</span><span class="value">: {{ $nomorSurat }}</span></div>
        <div class="row"><span class="label bold">Lampiran</span><span class="value">: Satu berkas</span></div>
        <div class="row"><span class="label bold">Perihal</span><span class="value">: Permohonan Kesediaan Menjadi Tim Penelaah</span></div>
        <div class="row no-indent"><span class="value" style="margin-left:105px;">Proposal Penelitian a.n {{ $namaMhs }} NIM. {{ $nim }}.</span></div>
    </div>

    <div class="mt">Kepada</div>
    <div class="row">
        <span class="value" style="margin-left:105px;">
            Yth. 1. {{ $penguji[0]['nama'] ?? '-' }}@if(!empty($penguji[0]['institusi'])) ({{ $penguji[0]['institusi'] }})@endif
        </span>
    </div>
    @for($i = 1; $i < count($penguji); $i++)
    <div class="row">
        <span class="value" style="margin-left:105px;">
            {{ $i + 1 }}. {{ $penguji[$i]['nama'] }}@if(!empty($penguji[$i]['institusi'])) ({{ $penguji[$i]['institusi'] }})@endif
        </span>
    </div>
    @endfor

    <p class="justify mt">
        Melalui surat dengan hormat kami sampaikan permohonan kiranya Bapak berkenan menjadi Tim
        Penelaah Proposal Penelitian Disertasi bagi mahasiswa Program Doktor {{ $prodi }}, yaitu:
    </p>

    <div class="row"><span class="label">Nama</span><span class="value">: {{ $namaMhs }}</span></div>
    <div class="row"><span class="label">NIM</span><span class="value">: {{ $nim }}</span></div>
    <div class="row"><span class="label">Judul Proposal Disertasi</span><span class="value">: {{ $judul }}</span></div>

    <div class="mt">dengan Tim Pembimbing:</div>
    @foreach($pembimbing as $i => $nama)
    <div class="row"><span class="value" style="margin-left:105px;">{{ $i + 1 }}. {{ $nama }}</span></div>
    @endforeach

    <div class="justify mt">
        Hasil penelaahan proposal penelitian disertasi mahasiswa yang bersangkutan, mohon dapat kembali kepada kami melalui email: sitijenab@fttm.itb.ac.id atau lilik@itb.ac.id, atau pada saat pelaksanaan Ujian Proposal Penelitian yang akan dilaksanakan pada tanggal {{ $tglHasilPenelaahan }} untuk diproses lebih lanjut.
    </div>

    <p class="justify">Atas perhatian dan kerja sama yang diberikan, kami haturkan terima kasih.</p>

    <div class="ttd">
        <div class="center" style="text-align:right;">tertanggal, {{ $tglPenelaah }}</div>
        <div style="text-align:right; margin-top:4px;">a.n. Dekan</div>
        <div style="text-align:right;">Wakil Dekan Bidang Akademik,</div>
        <div style="text-align:right; height:64px;"></div>
        <div style="text-align:right; font-weight:bold;">{{ $dekanNama }}</div>
        <div style="text-align:right;">NIP. {{ $dekanNip }}</div>
    </div>

    <div class="mt">
        <div class="bold">Tembusan, Yth.:</div>
        <div>1. &nbsp;Dekan FTTM-ITB (sebagai laporan);</div>
        <div>2. &nbsp;Ketua KPPs FTTM-ITB;</div>
        <div>3. &nbsp;Ketua Program Studi Magister dan Doktor {{ $prodi }} FTTM-ITB.</div>
    </div>
</body>
</html>