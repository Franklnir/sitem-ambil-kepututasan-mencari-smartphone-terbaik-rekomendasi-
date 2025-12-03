<!doctype html>
<html lang="en">

<?php
include 'components/head.php';
?>

<body>

  <div class="wrapper d-flex align-items-stretch">
    <?php
    include 'components/sidebar.php';
    ?>

    <!-- Page Content  -->
    <div id="content" class="p-4 p-md-5">

      <?php
      include 'components/navbar.php';
      ?>

      <section id="main-content">
        <section class="wrapper">
          <!--overview start-->
          <div class="row">
            <div class="col-lg-12">
              <ol class="breadcrumb">
                <li><i class="fa fa-sticky-note"></i><a href="kriteria.php"> Kriteria</a></li>
              </ol>
            </div>
          </div>

          <!--SCRIPT HITUNG PERBAIKAN BOBOT-->
          <script>
            function fungsiku() {
              var a = (document.getElementById("peringkat_param").value).substring(0, 1);
              var b = (document.getElementById("ukuran_param").value).substring(0, 1);
              var c = (document.getElementById("unduhan_param").value).substring(0, 1);
              var d = (document.getElementById("aktif_param").value).substring(0, 1);
              var e = (document.getElementById("manfaat_param").value).substring(0, 1);
              var f = (document.getElementById("kelebihan_param").value).substring(0, 1);
              var total = Number(a) + Number(b) + Number(c) + Number(d) + Number(e) + Number(f);

              document.getElementById("peringkat").value = (Number(a) / total).toFixed(2);
              document.getElementById("ukuran").value = (Number(b) / total).toFixed(2);
              document.getElementById("unduhan").value = (Number(c) / total).toFixed(2);
              document.getElementById("aktif").value = (Number(d) / total).toFixed(2);
              document.getElementById("manfaat").value = (Number(e) / total).toFixed(2);
              document.getElementById("kelebihan").value = (Number(f) / total).toFixed(2);
            }
          </script>
          <!--END SCRIPT HITUNG-->

          <!--START SCRIPT INSERT / UPDATE BOBOT-->
          <?php
          include 'koneksi.php';

          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
              $peringkatRaw = trim($_POST['peringkat'] ?? '');
              $ukuranRaw    = trim($_POST['ukuran'] ?? '');
              $unduhanRaw   = trim($_POST['unduhan'] ?? '');
              $aktifRaw     = trim($_POST['aktif'] ?? '');
              $manfaatRaw   = trim($_POST['manfaat'] ?? '');
              $kelebihanRaw = trim($_POST['kelebihan'] ?? '');

              if ($peringkatRaw === '' || $ukuranRaw === '' || $unduhanRaw === '' ||
                  $aktifRaw === '' || $manfaatRaw === '' || $kelebihanRaw === '') {
                  echo "<script>alert('Tolong lengkapi semua bobot kriteria!');</script>";
              } else {
                  $peringkat = (float)$peringkatRaw;
                  $ukuran    = (float)$ukuranRaw;
                  $unduhan   = (float)$unduhanRaw;
                  $aktif     = (float)$aktifRaw;
                  $manfaat   = (float)$manfaatRaw;
                  $kelebihan = (float)$kelebihanRaw;

                  $total = $peringkat + $ukuran + $unduhan + $aktif + $manfaat + $kelebihan;

                  if (abs($total - 1) > 0.001) {
                      echo "<script>alert('Total bobot harus = 1. Sekarang = " . $total . "');</script>";
                  } else {
                      // Simpan ke tabel kriteria_meta (kolom bobot)
                      $stmt = $conn->prepare("UPDATE kriteria_meta SET bobot = ? WHERE nama_kolom = ?");

                      if ($stmt) {
                          $dataBobot = [
                              'peringkat' => $peringkat,
                              'ukuran'    => $ukuran,
                              'unduhan'   => $unduhan,
                              'aktif'     => $aktif,
                              'manfaat'   => $manfaat,
                              'kelebihan' => $kelebihan,
                          ];

                          foreach ($dataBobot as $kolom => $bobot) {
                              $stmt->bind_param("ds", $bobot, $kolom);
                              $stmt->execute();
                          }

                          $stmt->close();
                          echo "<script>alert('Bobot kriteria berhasil disimpan!');</script>";
                      } else {
                          echo "<script>alert('Terjadi kesalahan saat menyimpan bobot.');</script>";
                      }
                  }
              }
          }

          // Ambil bobot saat ini untuk ditampilkan
          $bobotSaatIni = [
              'peringkat' => null,
              'ukuran'    => null,
              'unduhan'   => null,
              'aktif'     => null,
              'manfaat'   => null,
              'kelebihan' => null,
          ];

          $resBobot = $conn->query("SELECT nama_kolom, bobot FROM kriteria_meta");
          if ($resBobot) {
              while ($row = $resBobot->fetch_assoc()) {
                  $kolom = $row['nama_kolom'];
                  if (array_key_exists($kolom, $bobotSaatIni)) {
                      $bobotSaatIni[$kolom] = $row['bobot'];
                  }
              }
          }
          ?>
          <!-- END SCRIPT INSERT-->

          <!--start inputan-->
          <form class="form-validate form-horizontal" id="register_form" method="post" action="">
            <div class="form-group row">
              <label class="col-sm-2 col-form-label"><b>Kriteria</b></label>
              <div class="col-sm-3">
                <label><b>Bobot (Skala Prioritas)</b></label>
              </div>
              <div class="col-sm-2">
                <label><b>Perbaikan Bobot</b></label>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Performa (Skor Review / Benchmark)</label>
              <div class="col-sm-3">
                <select class="form-control" name="peringkat_param" id="peringkat_param">
                  <option>1. Sangat Rendah</option>
                  <option>2. Rendah</option>
                  <option>3. Cukup</option>
                  <option>4. Tinggi</option>
                  <option>5. Sangat Tinggi</option>
                </select>
              </div>
              <div class="col-sm-1">
                <input type="text" class="form-control" name="peringkat" id="peringkat"
                       value="<?php echo $bobotSaatIni['peringkat'] !== null ? $bobotSaatIni['peringkat'] : ''; ?>">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Harga Smartphone</label>
              <div class="col-sm-3">
                <select class="form-control" name="ukuran_param" id="ukuran_param">
                  <option>1. Sangat Murah</option>
                  <option>2. Murah</option>
                  <option>3. Sedang</option>
                  <option>4. Mahal</option>
                  <option>5. Sangat Mahal</option>
                </select>
              </div>
              <div class="col-sm-1">
                <input type="text" class="form-control" name="ukuran" id="ukuran"
                       value="<?php echo $bobotSaatIni['ukuran'] !== null ? $bobotSaatIni['ukuran'] : ''; ?>">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Kapasitas Baterai</label>
              <div class="col-sm-3">
                <select class="form-control" name="unduhan_param" id="unduhan_param">
                  <option>1. Sangat Kecil</option>
                  <option>2. Kecil</option>
                  <option>3. Sedang</option>
                  <option>4. Besar</option>
                  <option>5. Sangat Besar</option>
                </select>
              </div>
              <div class="col-sm-1">
                <input type="text" class="form-control" name="unduhan" id="unduhan"
                       value="<?php echo $bobotSaatIni['unduhan'] !== null ? $bobotSaatIni['unduhan'] : ''; ?>">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Kualitas Kamera</label>
              <div class="col-sm-3">
                <select class="form-control" name="aktif_param" id="aktif_param">
                  <option>1. Sangat Buruk</option>
                  <option>2. Buruk</option>
                  <option>3. Cukup</option>
                  <option>4. Baik</option>
                  <option>5. Sangat Baik</option>
                </select>
              </div>
              <div class="col-sm-1">
                <input type="text" class="form-control" name="aktif" id="aktif"
                       value="<?php echo $bobotSaatIni['aktif'] !== null ? $bobotSaatIni['aktif'] : ''; ?>">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Fitur Pendukung (NFC, 5G, dll)</label>
              <div class="col-sm-3">
                <select class="form-control" name="manfaat_param" id="manfaat_param">
                  <option>1. Sangat Sedikit</option>
                  <option>2. Sedikit</option>
                  <option>3. Cukup</option>
                  <option>4. Banyak</option>
                  <option>5. Sangat Banyak</option>
                </select>
              </div>
              <div class="col-sm-1">
                <input type="text" class="form-control" name="manfaat" id="manfaat"
                       value="<?php echo $bobotSaatIni['manfaat'] !== null ? $bobotSaatIni['manfaat'] : ''; ?>">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Ketersediaan & Layanan Purna Jual</label>
              <div class="col-sm-3">
                <select class="form-control" name="kelebihan_param" id="kelebihan_param">
                  <option>1. Sangat Buruk</option>
                  <option>2. Buruk</option>
                  <option>3. Cukup</option>
                  <option>4. Baik</option>
                  <option>5. Sangat Baik</option>
                </select>
              </div>
              <div class="col-sm-1">
                <input type="text" class="form-control" name="kelebihan" id="kelebihan"
                       value="<?php echo $bobotSaatIni['kelebihan'] !== null ? $bobotSaatIni['kelebihan'] : ''; ?>">
              </div>
              <div class="col-sm-2">
                <button class="btn btn-outline-success" type="button" id="hitung" onclick="fungsiku()" name="hitung">
                  <i class="fa fa-calculator"></i> Hitung
                </button>
              </div>
            </div>

            <div class="mb-4">
              <button class="btn btn-outline-primary" type="submit" name="submit">
                <i class="fa fa-save"></i> Submit
              </button>
            </div>
          </form>

          <table class="table">
            <thead>
              <tr>
                <th><i class="fa fa-arrow-down"></i> Performa</th>
                <th><i class="fa fa-arrow-down"></i> Harga</th>
                <th><i class="fa fa-arrow-down"></i> Kapasitas Baterai</th>
                <th><i class="fa fa-arrow-down"></i> Kualitas Kamera</th>
                <th><i class="fa fa-arrow-down"></i> Fitur Pendukung</th>
                <th><i class="fa fa-arrow-down"></i> Ketersediaan & Layanan</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td align="center">
                  <?php echo $bobotSaatIni['peringkat'] !== null ? $bobotSaatIni['peringkat'] : '-'; ?>
                </td>
                <td align="center">
                  <?php echo $bobotSaatIni['ukuran'] !== null ? $bobotSaatIni['ukuran'] : '-'; ?>
                </td>
                <td align="center">
                  <?php echo $bobotSaatIni['unduhan'] !== null ? $bobotSaatIni['unduhan'] : '-'; ?>
                </td>
                <td align="center">
                  <?php echo $bobotSaatIni['aktif'] !== null ? $bobotSaatIni['aktif'] : '-'; ?>
                </td>
                <td align="center">
                  <?php echo $bobotSaatIni['manfaat'] !== null ? $bobotSaatIni['manfaat'] : '-'; ?>
                </td>
                <td align="center">
                  <?php echo $bobotSaatIni['kelebihan'] !== null ? $bobotSaatIni['kelebihan'] : '-'; ?>
                </td>
              </tr>
            </tbody>
          </table>
        </section>
      </section>
    </div>
  </div>

  <script src="js/jquery.min.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
</body>

</html>
