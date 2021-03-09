<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Jurnal Umum</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="mt-5">
        <h3 class="text-center">{{$perusahaan->nama_perusahaan}}</h3>
        <h5 class="text-center">Jurnal Umum</h5>
        <h5 class="text-center">Periode X</h5>

        <table class="table mytable table-bordered mb-5">
            <thead class="thead-light">
                <tr class="table-primary text-center">
                    <th width="15%" scope="col">Tanggal</th>
                    <th width="55%" scope="col">Keterangan</th>
                    <th width="15%" scope="col">Debet (Rp)</th>
                    <th width="15%" scope="col">Kredit (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totaldebit = 0;
                    $totalkredit = 0;
                @endphp
                @foreach($jurnal as $data)
                @php
                    if($data->tipe === "D"){
                        $totaldebit = $totaldebit+$data->jumlah;
                    }else{
                        $totalkredit = $totalkredit+$data->jumlah;
                    }

                @endphp
                <tr>
                    <td class="text-center">{{date('j F Y', strtotime($data->tanggal))}}</td>
                    <td>{{ $data->nama_perkiraan }}</td>
                    <td class="text-right">{{ $data->tipe === "D" ? number_format($data->jumlah,2,',','.') : "0,00" }}</td>
                    <td class="text-right">{{ $data->tipe === "K" ? number_format($data->jumlah,2,',','.') : "0,00" }}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="text-center" colspan="2" style="font-weight:bold">Jumlah Total</td>
                    <td class="text-right" style="font-weight:bold">{{number_format($totaldebit,2,',','.')}}</td>
                    <td class="text-right" style="font-weight:bold">{{number_format($totalkredit,2,',','.')}}</td>
                </tr>
            </tbody>
        </table>

    </div>

</body>

</html>
