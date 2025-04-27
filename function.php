<?php
    
session_start();

$koneksi = mysqli_connect("localhost", "root", "localhost", "kasir");

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' and password='$password'");
    $hitung = mysqli_num_rows($check);

    if($hitung>0){
        //jika data ditemukan
        //berhasil login
        $_SESSION['login'] = 'True';
        header('location:index.php');
    } else{
        //data tidak ditemukan
        //gagal login
        echo '
        <script>alert("Username atau Password salah");
        window.location.href="login.php"
        </script>
        ';
    }
}

if(isset($_POST['tambahbarang'])){
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];

    $insert = mysqli_query($koneksi,"insert into produk (namaproduk,deskripsi,harga,stock) 
    values('$namaproduk','$deskripsi','$harga','$stock')");

    if($insert){
        header('location:stock.php');
    } else{
        echo '
        <script>alert("Gagal Menambah Data Barang");
        window.location.href="stock.php"
        </script>
        ';
    }

}


if(isset($_POST['tambahpelanggan'])){
    $namapelanggan = $_POST['namapelanggan'];
    $notelp = $_POST['notelp'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($koneksi,"insert into pelanggan (namapelanggan,notelp,alamat) 
    values('$namapelanggan','$notelp','$alamat')");

    if($insert){
        header('location:pelanggan.php');
    } else{
        echo '
        <script>alert("Gagal Menambah Data Pelanggan");
        window.location.href="pelanggan.php"
        </script>
        ';
    }

}


if(isset($_POST['tambahpesanan'])){
    $idpelanggan = $_POST['idpelanggan'];

    $insert = mysqli_query($koneksi,"insert into pesanan (idpelanggan) 
    values('$idpelanggan')");

    if($insert){
        header('location:index.php');
    } else{
        echo '
        <script>alert("Gagal Menambah Data Pelanggan");
        window.location.href="index.php"
        </script>
        ';
    }

}

//produk dipilih di pesanan
if(isset($_POST['addproduk'])){
    $idproduk = $_POST['idproduk'];
    $idp = $_POST['idp'];
    $qty = $_POST['qty'];

    //hitung stok sekarang
    $hitung1 = mysqli_query($koneksi, "select * from produk where idproduk='$idproduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stocksekarang = $hitung2['stock'];

    if($stocksekarang>=$qty){

        //kurangi stock
        $selisih = $stocksekarang-$qty;
        //stocknya cukup
        
    $insert = mysqli_query($koneksi,"insert into detail (idpesanan, idproduk, qty) 
    values('$idp', '$idproduk', '$qty')");
    $update = mysqli_query($koneksi, "update produk set stock='$selisih' where idproduk='$idproduk'");

    if($insert&&$update){
        header('location:view.php?idp='.$idp);
    } else{
        echo '
        <script>alert("Gagal Menambah Data Pelanggan");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';
    }

    } else {
        //stocknya gacukup
        echo '
        <script>alert("Stock Barang Tidak Cukup");
        window.location.href="view.php?idp='.$idp.'"
        </script>';
    }
}


//menambah barang masuk
if(isset(($_POST['barangmasuk']))){
    $idproduk = $_POST['idproduk'];
    $qty = $_POST['qty'];

    $insertb = mysqli_query($koneksi, "insert into masuk (idproduk, qty) values ('$idproduk','$qty')");

    if($insertb){
        header('location:masuk.php');
    } else {
    echo '
    <script>alert("Gagal");
    window.location.href="masuk.php"
    </script>
    ';
    }
}

//hapus produk pesanan
if(isset($_POST['hapusprodukpesanan'])){
    $idp = $_POST['idp'];
    $idpr = $_POST['idpr'];
    $idorder = $_POST['idorder'];

    //cek qty sekarang
    $cek1 = mysqli_query($koneksi,"select * from detail where iddetail='$idp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['$qty'];

    //cek sekarang
    $cek3 = mysqli_query($koneksi, "select * from produk where idproduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stocksekarang = $cek4['stock'];

    $hitung = $stocksekarang+$qtysekarang;

    $update = mysqli_query($koneksi, "update produk set stock='$hitung + ?' where idproduk='$idpr'"); //update stock
    $hapus = mysqli_query($koneksi, "delete from detail where idproduk='$idpr' and iddetail='$idp'");

    if ($update&&$hapus){
        header('location:view.php?idp='.$idorder);
    } else {
        echo '
        <script>alert("Gagal Menghapus Barang");
        window.location.href="view.php?idp='.$idorder.'"
        </script>
        ';      
    }
}

?>