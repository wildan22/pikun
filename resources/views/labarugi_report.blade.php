<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Laba Rugi</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    @php
        $response = json_decode($response, true);
    @endphp
    <div class="mt-5">
        <h3 class="text-center">{{$perusahaan->nama_perusahaan}}</h3>
        <h5 class="text-center">Laba Rugi</h5>
        <h5 class="text-center">Periode X</h5>

        <table class="table mytable table-bordered mb-5">
            <tbody>
                @foreach($response['data'] as $key => $val)
                    @foreach($response['data'][$key] as $d)
                    <tr>
                        <td style="font-weight:bold">{{ $d["rekening"] }}</td>
                        <td class="text-right"></td>
                    </tr>
                        @foreach($d['perkiraan'] as $p)
                        <tr>
                            <td>{{ $p['nama_perkiraan']}}</td>
                            {{-- <td class="text-right">{{number_format($p['jumlah'],2,',','.')}}</td> --}}
                            <td class="text-right">{{$p['jumlah']}}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="font-weight:bold">{{ $d["text"] }}</td>
                            {{-- <td style="font-weight:bold" class="text-right">{{number_format($d['total'],2,',','.')}}</td> --}}
                        </tr>
                    @endforeach
                    <tr class="table-active">
                        <td style="font-weight:bold">{{$response['text']}}</td>
                        {{-- <td style="font-weight:bold" class="text-right">{{number_format($response['total_keseluruhan'],2,',','.')}}</td> --}}
                    </tr>
                @endforeach
                
            </tbody>
        </table>

    </div>

</body>

</html>
