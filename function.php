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
        $updatestock = mysqli_query($koneksi,"UPDATE produk SET stock = stock + $qty WHERE idproduk = '$idproduk'");

        if ($updatestock){
            header('location:masuk.php');
        } else {
            echo '
                <script>alert("Gagal");
                window.location.href="masuk.php"
                </script>
            ';
        }

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
    $qtysekarang = $cek2['qty'];

    //cek sekarang
    $cek3 = mysqli_query($koneksi, "select * from produk where idproduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stocksekarang = $cek4['stock'];

    $hitung = $stocksekarang+$qtysekarang;

    $update = mysqli_query($koneksi, "update produk set stock='$hitung' where idproduk='$idpr'"); //update stock
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

//EDIT BARANG
if (isset($_POST['editbarang'])){
    $np = $_POST['namaproduk'];
    $desc = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $idp = $_POST['idp'];

    $query = mysqli_query($koneksi, "UPDATE produk SET namaproduk='$np', deskripsi='$desc', harga='$harga'  where idproduk='$idp'");

    if ($query){
        header('location:stock.php');
    } else {
    echo '
    <script>alert("Gagal");
    window.location.href="stock.php"
    </script>
    ';
    }
}

//Hapus Barang
if (isset($_POST['hapusbarang'])){
    $idp = $_POST['$idp'];

    $query = mysqli_query($koneksi, "DELETE from produk where idproduk='$idp'");

    if ($query){
        header('location:stock.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="stock.php"
        </script>
        ';
    }
}

//EDIT BARANG
if (isset($_POST['editpelanggan'])){
    $np = $_POST['namapelanggan'];
    $nt = $_POST['notelp'];
    $a = $_POST['alamat'];
    $id = $_POST['idpl'];

    $query = mysqli_query($koneksi, "UPDATE pelanggan SET namapelanggan='$np', notelp='$nt', alamat='$a'  where idpelanggan='$id'");

    if ($query){
        header('location:pelanggan.php');
    } else {
    echo '
    <script>alert("Gagal");
    window.location.href="pelanggan.php"
    </script>
    ';
    }
}

//Hapus Pelanggan
if (isset($_POST['hapuspelanggan'])){
    $idpl = $_POST['$idpl'];

    $query = mysqli_query($koneksi, "DELETE from pelanggan where idpelanggan='$idpl'");

    if ($query){
        header('location:pelanggan.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}

//Edit Data Masuk
if (isset($_POST['editmasuk'])){
    $qty = $_POST['qty'];
    $idm = $_POST['idm'];
    $idp = $_POST['idp'];

    //cari tau qty sekarang
    $caritahu = mysqli_query($koneksi," SELECT * From masuk where idmasuk='$idm'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang  = $caritahu2['qty'];

    //cari tau Stcok sekarang
    $caristock = mysqli_query($koneksi," SELECT * From produk where idproduk='$idp'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang  = $caristock2['stock'];

    if($qty  >= $qtysekarang){
        //kalau input userlebih besar daripada qty yg ada
        //hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stocksekarang+$selisih;
        
        $query1 = mysqli_query($koneksi, "UPDATE masuk set qty='$qty' where idmasuk='$idm'");
        $query2 = mysqli_query($koneksi, "UPDATE produk set stock='$newstock' where idproduk='$idp'");

        if ($query1&&$query2){
            header('location:masuk.php');
        } else {
        echo '
        <script>alert("Gagal");
        window.location.href="masuk.php"
        </script>
        ';
        }
    } else {
        //kalau lebih kecil
        $selisih = $qtysekarang-$qty;
        $newstock = $stocksekarang-$selisih;

        $query1 = mysqli_query($koneksi, "UPDATE masuk set qty='$qty' where idmasuk='$idm'");
        $query2 = mysqli_query($koneksi, "UPDATE produk set stock='$newstock' where idproduk='$idp'");

        if ($query1&&$query2){
            header('location:masuk.php');
        } else {
        echo '
        <script>alert("Gagal");
        window.location.href="masuk.php"
        </script>
        ';
        }
    }

}

//Hapus Barang Masuk
if (isset($_POST['hapusmasuk'])){
    $idp = $_POST['$idp'];
    $idm = $_POST['$idm'];

    //cari tau qty sekarang
    $caritahu = mysqli_query($koneksi," SELECT * From masuk where idmasuk='$idm'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang  = $caritahu2['qty'];

    //cari tau Stcok sekarang
    $caristock = mysqli_query($koneksi," SELECT * From produk where idproduk='$idp'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang  = $caristock2['stock'];

    //hitung selisih setelah dihapus
    $newstock = $stocksekarang-$qtysekarang;

    $query1 = mysqli_query($koneksi, "DELETE from masuk where idmasuk='$idm'");
    $query2 = mysqli_query($koneksi, "UPDATE produk set stock='$newstock' where idproduk='$idp'");

    if ($query1&&$query2){
        header('location:masuk.php');
    } else {
    echo '
    <script>alert("Gagal");
    window.location.href="masuk.php"
    </script>
    ';
    }
}

//Hapus Order
if (isset($_POST['hapusorder'])){
    $ido = $_POST['$ido'];

    $cekdata = mysqli_query($koneksi, "SELECT * From detail dp where idpesanan='$ido'");

    while($ok=mysqli_fetch_array($cekdata)){
        //balikin stock
        $qty = $ok['$qty'];
        $idproduk = $ok['idproduk'];
        $iddp = $ok['iddetail'];

        // cari tau stock sekarang
        $caristock = mysqli_query($koneksi, "SELECT * From produk where idproduk='$idproduk'");
        $caristock2 = mysqli_fetch_array($caristock);
        $stocksekarang = $caristock2['stock'];

        $newstock = $stocksekarang+$qty;

        $queryupdate = mysqli_query($koneksi, "UPDATE produk set stock='$newstock' where idproduk='$idproduk'");
        
        //hapus data 
        $querydelete = mysqli_query($koneksi, "DELETE from detail where iddetail='$iddp'");

    }

    $query = mysqli_query($koneksi, "DELETE from pesanan where idorder='$ido'");

    if ($queryupdate && $querydelete && $query){
        header('location:index.php');
    } else {
        echo '
        <script>alert("Gagal");
        window.location.href="index.php"
        </script>
        ';
    }
}

//Edit Data Detail Pesanan
if (isset($_POST['editdetail'])){
    $qty = $_POST['qty'];
    $iddp = $_POST['iddp']; //id masuk
    $idpr = $_POST['idpr']; //id produk
    $idp = $_POST['idp']; // id pesanan
    

    //cari tau qty sekarang
    $caritahu = mysqli_query($koneksi," SELECT * From detail where iddetail='$iddp'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang  = $caritahu2['qty'];

    //cari tau Stcok sekarang
    $caristock = mysqli_query($koneksi," SELECT * From produk where idproduk='$idpr'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stocksekarang  = $caristock2['stock'];

    if($qty  >= $qtysekarang){
        //kalau input user lebih besar daripada qty yg ada
        //hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stocksekarang-$selisih;
        
        $query1 = mysqli_query($koneksi, "UPDATE detail set qty='$qty' where iddetail='$iddp'");
        $query2 = mysqli_query($koneksi, "UPDATE produk set stock='$newstock' where idproduk='$idpr'");

        if ($query1&&$query2){
            header('location:view.php?idp='.$idp);
        } else {
        echo '
        <script>alert("Gagal");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';
        }
    } else {
        //kalau lebih kecil
        $selisih = $qtysekarang-$qty;
        $newstock = $stocksekarang+$selisih;

        $query1 = mysqli_query($koneksi, "UPDATE detail set qty='$qty' where iddetail='$iddp'");
        $query2 = mysqli_query($koneksi, "UPDATE produk set stock='$newstock' where idproduk='$idpr'");

        if ($query1&&$query2){
            header('location:view.php?idp='.$idp);
        } else {
        echo '
        <script>alert("Gagal");
        window.location.href="view.php?idp='.$idp.'"
        </script>
        ';
        }
    }

}

?>