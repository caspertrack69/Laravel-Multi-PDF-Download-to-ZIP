
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $data['tipe'] }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; padding: 20px; }
        h1 { text-align: center; }
        /* Tambahkan style lain sesuai kebutuhan */
    </style>
</head>
<body>
    @if($data['tipe'] === 'Invoice')
        <h1>INVOICE</h1>
        <hr>
        <p><strong>Nomor Invoice:</strong> {{ $data['nomor'] }}</p>
        <p><strong>Pelanggan:</strong> {{ $data['pelanggan'] }}</p>
        <p><strong>Total:</strong> {{ $data['total'] }}</p>
    @elseif($data['tipe'] === 'Laporan')
        <h1>{{ $data['judul'] }}</h1>
        <br><br>
        <p style="text-align:center;">Laporan ini dibuat dan disahkan oleh:</p>
        <br><br><br>
        <p style="text-align:center;"><strong>({{ $data['pembuat'] }})</strong></p>
    @elseif($data['tipe'] === 'Surat')
        <h3>SURAT RESMI</h3>
        <p><strong>Perihal:</strong> {{ $data['perihal'] }}</p>
        <p><strong>Kepada Yth:</strong><br>{{ $data['tujuan'] }}</p>
        <br>
        <p>Dengan hormat,</p>
        <p>...</p>
    @endif
</body>
</html>