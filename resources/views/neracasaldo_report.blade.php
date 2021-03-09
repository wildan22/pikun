<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Neraca Saldo</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    @php
    $response = json_decode($response, true);

    @endphp
    @foreach($response['data'] as $key => $val)
    <div class="mt-5">
        <h3 class="text-center">{{$perusahaan->nama_perusahaan}}</h3>
        <h5 class="text-center">Neraca Saldo</h5>
        <h5 class="text-center">Periode {{$key}}</h5>
        
        <table class="table mytable table-bordered mb-5">
            <thead class="thead-light">
                <tr class="table-primary text-center">
                    <th class="align-middle" rowspan="2" width="60%" scope="col">Nama Perkiraan</th>
                    <th colspan="2">{{$key}}</th>
                </tr>
                <tr class="table-primary text-center">
                    <th width="20%" scope="col">Debet (Rp)</th>
                    <th width="20%" scope="col">Kredit (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($response['data'][$key] as $d)
                <tr>
                    <td class="">{{$d['perkiraan']}}</td>
                    <td class="text-right">{{ $d['tipe'] === "D" ? number_format($d['jumlah'],2,',','.') : "0,00" }}</td>
                    <td class="text-right">{{ $d['tipe'] === "K" ? number_format($d['jumlah'],2,',','.') : "0,00" }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="font-weight:bold">Jumlah Total</td>
                    <td class="text-right" style="font-weight:bold">{{number_format($response['totaldebit'],2,',','.')}}</td>
                    <td class="text-right" style="font-weight:bold">{{number_format($response['totaldebit'],2,',','.')}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach

</body>

</html>
