<?php
    $penduduk = $_POST['id_penduduk'];
    $nilaik = $_POST['nilaik'];
    $conn   = koneksi();     

    mysqli_query($conn,"CREATE TEMPORARY TABLE RangkingSementara(
        Rangking int AUTO_INCREMENT primary key,
        Nama varchar(200),
        Usia int,
        Berat Decimal(10,2),
        Jarak Decimal(10,4),
        Status char(1));
    ");

    mysqli_query($conn,"CREATE TEMPORARY TABLE Kesimpulan(
        Nomor int AUTO_INCREMENT primary key,
        Nama varchar(200),
        Usia int,
        Berat Decimal(10,2),
        Jarak Decimal(10,4),
        Status int(1));
    ");

    // Ambil nomor urut terbesar
    $nomor    = mysqli_query($conn,"SELECT max(nomor_urut) as max FROM detail_penduduk WHERE id_penduduk='$penduduk';");
    $nomormax = mysqli_fetch_array($nomor);
    $maksimum = $nomormax['max'];

    // Data uji balita
    $testing  = mysqli_query($conn,"SELECT * FROM penduduk 
                  INNER JOIN detail_penduduk ON penduduk.id_penduduk = detail_penduduk.id_penduduk
                  WHERE penduduk.id_penduduk='$penduduk' AND detail_penduduk.nomor_urut='$maksimum';");
    $datates  = mysqli_fetch_array($testing);
    $gender   = $datates['jenis_kelamin'];

    // Data latih balita
    $training = mysqli_query($conn,"SELECT * FROM penduduk 
                  INNER JOIN detail_penduduk ON penduduk.id_penduduk = detail_penduduk.id_penduduk
                  WHERE penduduk.jenis_data='0' AND penduduk.jenis_kelamin='$gender';");      

    while ($datatrain = mysqli_fetch_array($training)) {
      // Variable data latih
      $usia   = $datatrain['usia'];
      $berat  = $datatrain['berat_badan'];

      // Variabledata uji
      $usiates = $datates['usia'];
      $bbtes   = $datates['berat_badan'];

      // Hitung jarak setiap sampel
      $rumus = sqrt(pow(($usiates-$usia), 2) + pow(($bbtes-$berat), 2));

      // Simpan hasil hitung jarak
      mysqli_query($conn,"INSERT INTO RangkingSementara (Nama,Usia,Berat,Jarak,Status)
      VALUES ('".$datatrain['nama_penduduk']."','".$usia."','".$berat."',".$rumus.",'".$datatrain['status_sehat']."'); ");
    }

    // Urutkan jarak dari yang terkecil
    $rangking = mysqli_query($conn,"SELECT * FROM RangkingSementara ORDER BY Jarak ASC LIMIT $nilaik;");      
    while ($datas = mysqli_fetch_array($rangking)) {
      // Simpan kesimpulan
      mysqli_query($conn,"INSERT INTO Kesimpulan (Nama,Usia,Berat,Jarak,Status)
      VALUES ('".$datas['Nama']."','".$datas['Usia']."','".$datas['Berat']."',".$datas['Jarak'].",'".$datas['Status']."'); ");
    }

?> 
	  <div class="table-responsive">
	  	<h3>Kesimpulan Hasil Prediksi</h3>
      
	    <table class="table table-bordered">
	      <thead class='thead-light'>
	        <tr>
	          <th style="width: 250px">Nama Penduduk</th>
	          <th>Usia</th>
	          <th>Berat Badan</th>
	          <th>Jenis Kelamin</th>
	          <th>Jarak Terdekat (km)</th>
	          <th>Kesimpulan</th>
	        </tr>
	      </thead>

	      <tbody>
	        <?php
	          // Mengambil kategori terbanyak
	          $kesimpulan = mysqli_query($conn,"SELECT Status, count(Status) as jumlah 
	                                            FROM Kesimpulan GROUP BY Status ORDER BY jumlah DESC");      
	          $data = mysqli_fetch_array($kesimpulan);
	          $status = "";
	          $warna  = "";
	            if($data['Status'] == 1) {$status = "OTG (Orang Tanpa Gejala)";$warna = "badge-info";}
	            if($data['Status'] == 2) {$status = "ODP (Orang Dalam Pemantauan)";$warna = "badge-success";}
	            if($data['Status'] == 3) {$status = "PDP (Pasien Dalam Pengawasan)";$warna = "badge-warning";}
	            if($data['Status'] == 4) {$status = "Suspect COVID-19";$warna = "badge-danger";}   
	        ?>                
	          <tr>
	            <td><?=$datates["nama_penduduk"]?></td>
	            <td><?=$datates["usia"]?> Tahun</td>
	            <td><?=$datates["berat_badan"]?> Kg</td>
	            <td><?php 
	                  $jk = "Laki-laki";
	                  if($datates["jenis_kelamin"] == "P"){
	                    $jk = "Perempuan";
	                  } echo $jk;
	                ?>                              
	            </td>
	            <td><span id="nilaik"><?=$nilaik?></span></td>
	            <td>
	              <span class="badge <?=$warna?>" style="font-size: 12px;"><?=$status?></span>
	              <span class="invisible" id="id_penduduk"><?=$datates["id_penduduk"]?></span>
	              <span class="invisible" id="status"><?=$data["Status"]?></span>
	            </td>
	          </tr>                
	      </tbody>
	    </table>

	    <div class="col-md-12" style="text-align:center;margin-top: 20px;margin-bottom: 30px">                        
	      <button type="submit" id="button" name="simpan" class="btn btn-primary">SIMPAN</button>                 
	    </div>
	  </div> <!-- responsive -->   

	  <h3 class="box-title">Jarak Terdekat (k = <?=$nilaik?>) km</h3>
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class='thead-light'>
          <tr>
            <th style="width: 50px">Rangking</th>
            <th>Nama Penduduk</th>
            <th>Usia</th>
            <th>Berat Badan</th>
            <th>Status Kesehatan</th>
            <th>Jarak Prediksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
            // Mengambil kategori terbanyak
            $jarak = mysqli_query($conn,"SELECT * FROM Kesimpulan");      
            while ($data = mysqli_fetch_array($jarak)) {  
            $status = "";
            $warna  = "";
              if($data['Status'] == 1) {$status = "OTG (Orang Tanpa Gejala)";$warna = "badge-info";}
              if($data['Status'] == 2) {$status = "ODP (Orang Dalam Pemantauan)";$warna = "badge-success";}
              if($data['Status'] == 3) {$status = "PDP (Pasien Dalam Pengawasan)";$warna = "badge-warning";}
              if($data['Status'] == 4) {$status = "Suspect COVID-19";$warna = "badge-danger";}                
          ?>                
            <tr>
              <td><?=$data["Nomor"]?></td>
              <td><?=$data["Nama"]?></td>
              <td><?=$data["Usia"]?> Tahun</td>
              <td><?=$data["Berat"]?> Kg</td>
              <td><span class="badge <?=$warna?>" style="font-size: 12px;"><?=$status?></span></td>
              <td><?=$data["Jarak"]?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
   </div> <!-- responsive -->	   

    