<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Buku Besar</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
    <div class="mt-5">
        <h2 class="text-center"></h2>
        <h5 class="text-center">Laporan Buku Besar</h5>
        <h5 class="text-center">{{$namaperkiraan}}</h5>
        <h5 class="text-center">Periode X</h5>


        @php
        $response = json_decode($response, true);
        @endphp

        @foreach($response['data'] as $key => $val)
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-primary">
                    <th scope="col">Tanggal</th>
                    <th scope="col">Keterangan</th>
                    <th scope="col">Debet</th>
                    <th scope="col">Kredit</th>
                    <th scope="col">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($response['data'][$key] as $data)
                <tr>
                    <th scope="row">{{ $data['tanggal'] }}</th>
                    <td>{{ $data['keterangan'] }}</td>
                    <td>{{ $data['jenis'] === "D" ? $data['jumlah'] : "0" }}</td>
                    <td>{{ $data['jenis'] === "K" ? $data['jumlah'] : "0" }}</td>
                    <td>{{ $data['total'] }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2">Total</td>
                    <td>{{$response['totaldebit']}}</td>
                    <td>{{$response['totalkredit']}}</td>
                    <td>{{$response['totalsaldo']}}</td>
                </tr>
            </tbody>
        </table>
        @endforeach

    </div>

</body>

</html>
