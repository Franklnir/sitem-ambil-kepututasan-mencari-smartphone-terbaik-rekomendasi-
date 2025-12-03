<!doctype html>
<html lang="en">

<?php
include 'components/head.php';
?>

<body>
  <div class="wrapper d-flex align-items-stretch">
    <?php include 'components/sidebar.php'; ?>

    <!-- Page Content  -->
    <div id="content" class="p-4 p-md-5">
      <?php include 'components/navbar.php'; ?>

      <section id="main-content">
        <section class="wrapper">
          <!--overview start-->
          <div class="row">
            <div class="col-lg-12">
              <ol class="breadcrumb">
                <li><i class="fa fa-list-ol"></i><a href="penilaian.php"> Penilaian</a></li>
              </ol>
            </div>
          </div>

          <!-- START SCRIPT INSERT -->
          <?php
          include 'koneksi.php';

          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
              $nama     = $_POST['nama'] ?? '';
              $peringkat = (float) ($_POST['peringkat'] ?? 0);
              $ukuran   = (int) substr($_POST['ukuran'] ?? '', 1, 1);
              $unduhan  = (int) substr($_POST['unduhan'] ?? '', 1, 1);
              $aktif    = (int) substr($_POST['aktif'] ?? '', 1, 1);
              $manfaat  = (int) substr($_POST['manfaat'] ?? '', 1, 1);
              $kelebihan= (int) substr($_POST['kelebihan'] ?? '', 1, 1);

              if ($nama === '' || $peringkat == 0 || $ukuran == 0 || $unduhan == 0 ||
                  $aktif == 0 || $manfaat == 0 || $kelebihan == 0) {
                  echo "<script>alert('Tolong lengkapi semua data penilaian!');</script>";
              } else {
                  // Cek apakah alternatif sudah pernah dinilai
                  $stmt = $conn->prepare("SELECT 1 FROM saw_penilaian WHERE nama = ?");
                  if ($stmt) {
                      $stmt->bind_param("s", $nama);
                      $stmt->execute();
                      $result = $stmt->get_result();

                      if ($result && $result->num_rows > 0) {
                          echo "<script>alert('Smartphone $nama sudah memiliki penilaian!');</script>";
                      } else {
                          $stmtInsert = $conn->prepare(
                              "INSERT INTO saw_penilaian
                               (nama, peringkat, ukuran, unduhan, aktif, manfaat, kelebihan)
                               VALUES (?, ?, ?, ?, ?, ?, ?)"
                          );
                          if ($stmtInsert) {
                              $stmtInsert->bind_param(
                                  "siiiiii",
                                  $nama, $peringkat, $ukuran, $unduhan, $aktif, $manfaat, $kelebihan
                              );
                              $stmtInsert->execute();
                              echo "<script>alert('Penilaian berhasil ditambahkan!');</script>";
                          } else {
                              echo "<script>alert('Gagal menyiapkan query INSERT.');</script>";
                          }
                      }
                  } else {
                      echo "<script>alert('Gagal menyiapkan query SELECT.');</script>";
                  }
              }
          }
          ?>
          <!-- END SCRIPT INSERT -->

          <!-- Form input penilaian -->
          <form method="POST" action="">
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Smartphone (Alternatif)</label>
              <div class="col-sm-4">
                <select class="form-control" name="nama">
                  <?php
                  $sqlAlt  = "SELECT * FROM saw_aplikasi ORDER BY nama ASC";
                  $hasilAlt = $conn->query($sqlAlt);

                  if ($hasilAlt && $hasilAlt->num_rows > 0) {
                      while ($row = $hasilAlt->fetch_assoc()) {
                          echo '<option>' . htmlspecialchars($row['nama']) . '</option>';
                      }
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Performa & Ulasan (rating 1–5)</label>
              <div class="col-sm-4">
                <input type="number" min="1" max="5" step="0.1"
                       class="form-control" name="peringkat" id="peringkat"
                       placeholder="Contoh: 4.5">
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Harga</label>
              <div class="col-sm-4">
                <select class="form-control" name="ukuran">
                  <option>(1) Sangat Mahal</option>
                  <option>(2) Mahal</option>
                  <option>(3) Sedang</option>
                  <option>(4) Murah</option>
                  <option>(5) Sangat Murah</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Kapasitas Baterai</label>
              <div class="col-sm-4">
                <select class="form-control" name="unduhan">
                  <option>(1) Sangat Kecil</option>
                  <option>(2) Kecil</option>
                  <option>(3) Sedang</option>
                  <option>(4) Besar</option>
                  <option>(5) Sangat Besar</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Kamera</label>
              <div class="col-sm-4">
                <select class="form-control" name="aktif">
                  <option>(1) Sangat Buruk</option>
                  <option>(2) Buruk</option>
                  <option>(3) Cukup</option>
                  <option>(4) Baik</option>
                  <option>(5) Sangat Baik</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Fitur Pendukung</label>
              <div class="col-sm-4">
                <select class="form-control" name="manfaat">
                  <option>(1) Sangat Sedikit</option>
                  <option>(2) Sedikit</option>
                  <option>(3) Sedang</option>
                  <option>(4) Banyak</option>
                  <option>(5) Sangat Banyak</option>
                </select>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Ketersediaan / After Sales</label>
              <div class="col-sm-4">
                <select class="form-control" name="kelebihan">
                  <option>(1) Sangat Sulit</option>
                  <option>(2) Sulit</option>
                  <option>(3) Sedang</option>
                  <option>(4) Mudah</option>
                  <option>(5) Sangat Mudah</option>
                </select>
              </div>
            </div>

            <div class="mb-4">
              <button type="submit" name="submit" class="btn btn-outline-primary">
                <i class="fa fa-save"></i> Submit
              </button>
            </div>
          </form>

          <!-- Tabel penilaian -->
          <table class="table">
            <thead>
              <tr>
                <th><i class="fa fa-arrow-down"></i> No</th>
                <th><i class="fa fa-arrow-down"></i> Smartphone</th>
                <th><i class="fa fa-arrow-down"></i> Performa & Ulasan</th>
                <th><i class="fa fa-arrow-down"></i> Harga</th>
                <th><i class="fa fa-arrow-down"></i> Kapasitas Baterai</th>
                <th><i class="fa fa-arrow-down"></i> Kamera</th>
                <th><i class="fa fa-arrow-down"></i> Fitur Pendukung</th>
                <th><i class="fa fa-arrow-down"></i> Ketersediaan</th>
                <th><i class="fa fa-cogs"></i> Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $b = 0;
              $sql  = "SELECT * FROM saw_penilaian ORDER BY nama ASC";
              $hasil = $conn->query($sql);

              if ($hasil && $hasil->num_rows > 0) {
                  while ($row = $hasil->fetch_row()) {
                      ?>
                      <tr>
                        <td align="center"><?php echo ++$b; ?></td>
                        <td><?= htmlspecialchars($row[0]); ?></td>
                        <td align="center"><?= $row[1]; ?></td>
                        <td align="center"><?= $row[2]; ?></td>
                        <td align="center"><?= $row[3]; ?></td>
                        <td align="center"><?= $row[4]; ?></td>
                        <td align="center"><?= $row[5]; ?></td>
                        <td align="center"><?= $row[6]; ?></td>
                        <td>
                          <div class="btn-group">
                            <a class="btn btn-danger"
                               href="penilaian_hapus.php?nama=<?= urlencode($row[0]); ?>"
                               onclick="return confirm('Yakin hapus penilaian untuk smartphone ini?');">
                              <i class="fa fa-close"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                      <?php
                  }
              } else {
                  echo "<tr><td colspan='9'>Data tidak ada</td></tr>";
              }
              ?>
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
