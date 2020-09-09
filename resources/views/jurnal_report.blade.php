<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report Jurnal Umum</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Perusahaan Maju Mundur</h2>
        <h5 class="text-center">Jalanin Aja Dulu ....</h5>

        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-primary">
                    <th scope="col">Tanggal</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Debet</th>
                    <th scope="col">Kredit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jurnal as $data)
                <tr>
                    <th scope="row">{{ $data->tanggal }}</th>
                    <td>{{ $data->nama_perkiraan }}</td>
                    <td>{{ $data->tipe === "D" ? $data->jumlah : "0" }}</td>
                    <td>{{ $data->tipe === "K" ? $data->jumlah : "0" }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</body>

</html>
