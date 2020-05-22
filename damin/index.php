<?php require_once 'config/koneksi.php'; ?>

<!DOCTYPE html>
<html>
<head>
	<title>Data Mining KNN</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">	
	<link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet" />	
</head>
<body>
	
    <!-- Full Width Column -->
 	<div class="container" style="margin-top: 50px">
 		
        <!-- Main content -->
		<div id="simpan"></div>  

        <div class="col-md-12">
			<form class="form-horizontal" method="POST">				
				<hr>
					<h3>Prediksi Status Kesehatan Penduduk Desa Shanghai Masa COVID-19</h3>
				<hr>				
				<div class="form-group">
				  <label class="col-sm-6 control-label">Nama Penduduk :</label>
				  <div class="col-sm-6">
				    <select class="form-control select2" name="id_penduduk" required>
	                  <option value="" readonly >Nama Penduduk</option>
	                  <?php                
	                    $conn = koneksi();                   
	                    $sql  = "SELECT * FROM penduduk 
	                    		 inner join detail_penduduk on penduduk.id_penduduk = detail_penduduk.id_penduduk
	                    		 group by penduduk.id_penduduk";
	                    $hasil = mysqli_query($conn, $sql);
	                    while ($r = mysqli_fetch_array($hasil)) { 
						  $gender = "Perempuan";
						  if($r["jenis_kelamin"] == "L"){
						    $gender = "Laki-laki";
						  }
	                  ?>
	                  <option 
	                  	value="<?=$r['id_penduduk']?>">
	                  	<?php echo 
	                  		$r['nama_penduduk'].' | '.$gender.' | '.$r['berat_badan'].' Kg | '.$r['usia'].' Tahun';
	                  	?>	                  		
	                  </option>
	                  <?php } ?>
	                </select>
				  </div>
				</div>				
				<div class="form-group">
				  <label class="col-sm-4 control-label">Tetangga Suspect Terdekat (km) :</label>
				  <div class="col-sm-4">
				    <select class="form-control select2" name="nilaik" required>
				      <option value="" readonly >Pilih</option>                            
				      <option>3</option>
				      <option>5</option>
				      <option>7</option>
				      <option>9</option>
				      <option>11</option>
				      <option>13</option>
				    </select>
				  </div>
				</div>
				<div class="col-sm-6" style="margin-top: 20px;margin-bottom: 30px">
					<button type="submit" id="hitung" name="hitung" class="btn btn-primary">HITUNG</button>
				</div>
			</form>

			<!-- Munculkan hasil -->
	        <?php 
	          if(isset($_POST["hitung"])){ 
	            include_once('proses.php'); 
	          } 
	         ?>

		</div> <!-- /.col 7 -->
	</div> <!-- /.container -->

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  	<script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>

	<script>
		$(function () {
		  $('.select2').select2()
		})

		$("#button").click(function () {
			var id = $('#id_penduduk').text();
			var nilaik = $('#nilaik').text();
			var status = $('#status').text() ;
			$.ajax ({
			  type: "POST",
			  url: "simpan.php",
			  data: 'id_penduduk=' + id + '&nilaik=' + nilaik +'&hasil_prediksi='+status,
			  success: function (respons) {
			    $('#simpan').html(respons);
			    $('html, body').animate({
			    scrollTop : 0},1500);
			    return false;
			  }
			})
		})  
    </script>

</body>
</html>