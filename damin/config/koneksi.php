<?php
function koneksi() {
	$servername = "localhost";
	$username   = "root";
	$password   = "";
	$db 	    = "datamining";

	$koneksi = mysqli_connect($servername, $username, $password, $db);

	if (!$koneksi) {
		die("Koneksi gagal: " . mysqli_connect_error());
	}
	return $koneksi;
}
?>