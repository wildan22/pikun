<?php

use Illuminate\Database\Seeder;
use App\JenisTransaksi;
use App\Rekening;
use App\Perkiraan;
use App\Mapping;
class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //SEEDING JENIS TRANSAKSI
        $jt = ['Pemasukan','Pengeluaran','Utang','Bayar Utang','Piutang','Dibayar Piutang','Tambah Modal','Tarik Modal','Pengalihan Aset','Penyesuaian'];
        $ket1 = ['Diterima Dari','Diambil Dari','Utang Dari','X','Dari','X','Modal','Diambil Dari','Dari','Dari'];
        $ket2 = ['Simpan Ke','Untuk','Simpan Ke','X','Simpan Ke','X','Simpan Ke','Modal','Ke','Ke'];
        for($i=0 ; $i<10 ; $i++){
            $jenistransaksi = new JenisTransaksi;
            $jenistransaksi->jenis_transaksi = $jt[$i];
            $jenistransaksi->keterangan1 = $ket1[$i];
            $jenistransaksi->keterangan2 = $ket2[$i];
            $jenistransaksi->save();
        }
        
        //SEEDING REKENING
        $namarek = ['Aktiva Lancar','Aktiva Tetap','Akumulasi Penyusutan','Utang Jangka Pendek','Utang Jangka Panjang','Modal','Pendapatan','Harga Pokok Penjualan','Biaya Penjualan','Biaya Admin dan Umum','Pendapatan Diluar Usaha','Biaya Diluar Usaha'];
        $tiperek = ['D','D','K','K','K','K','K','D','D','D','K','D'];

        for($i=0 ; $i<12 ; $i++){
            $rekening = new Rekening;
            $rekening->nama_rekening = $namarek[$i];
            $rekening->tipe = $tiperek[$i];

            $rekening->save();
        }

        //SEEDING PERKIRAAN
        $namaperkiraan = [
            'Kas'
            ,'Bank'
            ,'Perlengkapan'
            ,'Persediaan Bahan Baku'
            ,'Persediaan Bahan Dagang'
            ,'Piutang Usaha'
            ,'Sewa Dibayar Dimuka'
            ,'Tanah'
            ,'Bangunan'
            ,'Kendaraan'
            ,'Peralatan'
            ,'Akumulasi Penyusutan Kendaraan'
            ,'Akumulasi Penyusutan Peralatan'
            ,'Akumulasi Penyusutan Bangunan'
            ,'Utang Usaha'
            ,'Utang Bank'
            ,'Modal Pemilik'
            ,'Prive'
            ,'Pendapatan'
            ,'Penjualan Barang'
            ,'Ikhtisiar Laba/Rugi'
            ,'Potongan Penjualan'
            ,'Retur Penjualan'
            ,'Harga Pokok Penjualan'
            ,'Potongan Pembelian'
            ,'Retur Pembelian'
            ,'Biaya Pengiriman'
            ,'Biaya Penjualan Lain-lain'
            ,'Biaya Air'
            ,'Biaya Depresiasi Peralatan'
            ,'Biaya Gaji Karyawan'
            ,'Biaya Listrik'
            ,'Biaya Makan dan Minum'
            ,'Biaya Perlengkapan'
            ,'Biaya Sewa Tempat Usaha'
            ,'Biaya Telepon'
            ,'Biaya Umum Lain-lain'
        ];

        $rekeningid = [
            1
            ,1
            ,1
            ,1
            ,1
            ,1
            ,1
            ,2
            ,2
            ,2
            ,2
            ,3
            ,3
            ,3
            ,4
            ,5
            ,6
            ,6
            ,7
            ,7
            ,7
            ,7
            ,7
            ,8
            ,8
            ,8
            ,9
            ,9
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
        ];

        for($i=0;$i<37;$i++){
            $perkiraan = New Perkiraan;
            $perkiraan->nama_perkiraan = $namaperkiraan[$i];
            $perkiraan->rekening_id = $rekeningid[$i];
            $perkiraan->save();
        }

        //SEEDING MAPPING
        $transaksi_id = [
            1
            ,1
            ,2
            ,2
            ,2
            ,2
            ,2
            ,2
            ,2
            ,3
            ,3
            ,3
            ,3
            ,3
            ,3
            ,5
            ,5
            ,5
            ,5
            ,7
            ,7
            ,7
            ,8
            ,8
            ,8
            ,9
            ,9
            ,9
            ,9
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
            ,10
        ];
        $rekening_id = [
            7
            ,1
            ,1
            ,2
            ,10
            ,9
            ,8
            ,2
            ,1
            ,4
            ,5
            ,1
            ,2
            ,9
            ,10
            ,7
            ,6
            ,1
            ,1
            ,6
            ,1
            ,2
            ,1
            ,2
            ,6
            ,1
            ,2
            ,1
            ,2
            ,1
            ,2
            ,3
            ,4
            ,5
            ,7
            ,8
            ,9
            ,10
            ,1
            ,2
            ,3
            ,4
            ,5
            ,7
            ,8
            ,9
            ,10
            ,
        ];

        $tipemap = [
            'D'
            ,'K'
            ,'D'
            ,'D'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,'D'
            ,'D'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,'D'
            ,'D'
            ,'D'
            ,'K'
            ,'D'
            ,'K'
            ,'K'
            ,'D'
            ,'D'
            ,'K'
            ,'D'
            ,'D'
            ,'K'
            ,'K'
            ,'D'
            ,'D'
            ,'D'
            ,'D'
            ,'D'
            ,'D'
            ,'D'
            ,'D'
            ,'D'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,'K'
            ,
        ];

        for($i=0 ; $i<47;$i++){
            $mapping = New Mapping;
            $mapping->transaksi_id = $transaksi_id[$i];
            $mapping->rekening_id = $rekening_id[$i];
            $mapping->tipe = $tipemap[$i];
            $mapping->save();
        }
    }
}
