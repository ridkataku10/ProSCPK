<?php
	include_once 'config/koneksi.php';

    $id     = $_POST["id_penduduk"];
	$nilaik = $_POST['nilaik'];
    $hasil  = $_POST['hasil_prediksi'];
    $date   = date("Y-m-d");

    $conn = koneksi();
	$cek  = mysqli_query($conn,"SELECT id_penduduk FROM prediksi WHERE id_penduduk='$id' AND nilaik='$nilaik'");

	if (mysqli_num_rows($cek) <> 0) {
		$simpan = mysqli_query($conn,"UPDATE prediksi set 
			hasil_prediksi='$hasil', tanggal='$date' WHERE id_penduduk='$id' AND nilaik='$nilaik'");
	}
	else {
    	$simpan = mysqli_query($conn,"INSERT INTO prediksi (id_penduduk, nilaik, hasil_prediksi, tanggal) 
    		VALUES (".$id.",'".$nilaik."','".$hasil."','".$date."')");
	}

	if($simpan) {
		echo "<div class='alert alert-success col-md-12' role='alert'>
				<a href='#' class='close' data-dismiss='alert' aria-label='close'>×</a>
				<h5><i class='icon fa fa-check'></i> Data Berhasil Disimpan!</h5>
			  </div>";		
	}
	else {
		echo "<div class='alert alert-danger col-md-12' role='alert'>
				<a href='#' class='close' data-dismiss='alert' aria-label='close'>×</a>
				<h5><i class='icon fa fa-ban'></i> Gagal Simpan Data!</h5> 
			  </div>";
	}
?>