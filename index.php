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
                <li><i class="fa fa-user"></i><a href="index.php"> Alternatif Smartphone</a></li>
              </ol>
            </div>
          </div>

          <!--START SCRIPT INSERT-->
          <?php
          include 'koneksi.php';

          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
              $nama       = trim($_POST['nama'] ?? '');
              $pengembang = trim($_POST['pengembang'] ?? '');
              $kategori   = trim($_POST['kategori'] ?? '');

              if ($nama === '' || $pengembang === '') {
                  echo "<script>alert('Tolong lengkapi data yang ada!');</script>";
              } else {
                  // Cek apakah smartphone sudah ada (berdasarkan nama)
                  $stmt = $conn->prepare("SELECT 1 FROM saw_aplikasi WHERE nama = ?");
                  if ($stmt) {
                      $stmt->bind_param("s", $nama);
                      $stmt->execute();
                      $result = $stmt->get_result();

                      if ($result && $result->num_rows > 0) {
                          echo "<script>alert('Smartphone dengan nama $nama sudah ada!');</script>";
                      } else {
                          // Insert data alternatif baru
                          $stmtInsert = $conn->prepare(
                              "INSERT INTO saw_aplikasi (nama, pengembang, kategori) VALUES (?, ?, ?)"
                          );
                          if ($stmtInsert) {
                              $stmtInsert->bind_param("sss", $nama, $pengembang, $kategori);
                              $stmtInsert->execute();
                              echo "<script>alert('Data smartphone berhasil ditambahkan!');</script>";
                              $stmtInsert->close();
                          } else {
                              echo "<script>alert('Terjadi kesalahan saat menyimpan data.');</script>";
                          }
                      }

                      $stmt->close();
                  } else {
                      echo "<script>alert('Terjadi kesalahan pada query database.');</script>";
                  }
              }
          }
          ?>
          <!-- END SCRIPT INSERT-->

          <!--start inputan-->
          <form method="POST" action="">
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Nama Smartphone</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="nama" placeholder="contoh: Galaxy A55">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Merk / Brand</label>
              <div class="col-sm-5">
                <input type="text" class="form-control" name="pengembang" placeholder="contoh: Samsung, Xiaomi, dll">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Kategori Smartphone</label>
              <div class="col-sm-5">
                <select class="form-control" name="kategori">
                  <option>Flagship</option>
                  <option>Mid-range</option>
                  <option>Entry-level</option>
                  <option>Gaming</option>
                  <option>Camera-centric</option>
                  <option>Battery-centric</option>
                  <option>Lainnya</option>
                </select>
              </div>
            </div>
            <div class="mb-4">
              <button type="submit" name="submit" class="btn btn-outline-primary">
                <i class="fa fa-save"></i> Submit
              </button>
            </div>
          </form>

          <table class="table">
            <thead>
              <tr>
                <th><i class="fa fa-arrow-down"></i> No</th>
                <th><i class="fa fa-arrow-down"></i> Nama Smartphone</th>
                <th><i class="fa fa-arrow-down"></i> Merk / Brand</th>
                <th><i class="fa fa-arrow-down"></i> Kategori</th>
                <th><i class="fa fa-cogs"></i> Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sqlList = "SELECT * FROM saw_aplikasi ORDER BY nama ASC";
              $hasilList = $conn->query($sqlList);
              $no = 0;

              if ($hasilList && $hasilList->num_rows > 0) {
                  while ($row = $hasilList->fetch_assoc()) {
                      $no++;
                      ?>
                      <tr>
                        <td><?php echo $no; ?></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['pengembang']); ?></td>
                        <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                        <td>
                          <div class="btn-group">
                            <a class="btn btn-success"
                               href="alt_ubah.php?nama=<?php echo urlencode($row['nama']); ?>">
                              <i class="fa fa-edit"></i>
                            </a>
                            <a class="btn btn-danger"
                               href="alt_hapus.php?nama=<?php echo urlencode($row['nama']); ?>"
                               onclick="return confirm('Hapus smartphone ini?');">
                              <i class="fa fa-trash"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                      <?php
                  }
              } else {
                  echo "<tr><td colspan='5'>Data tidak ada</td></tr>";
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
