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
                                <li><i class="fa fa-cogs"></i><a href="hitung.php"> Hitung</a></li>
                            </ol>
                        </div>
                    </div>

                    <?php
                    include 'koneksi.php';
                    ?>

                    <!-- MATRIX X -->
                    <div>
                        <h6><b>MATRIX X</b></h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Smartphone</th>
                                    <th>Performa & Ulasan</th>
                                    <th>Harga</th>
                                    <th>Kapasitas Baterai</th>
                                    <th>Kamera</th>
                                    <th>Fitur Pendukung</th>
                                    <th>Ketersediaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $b = 0;
                                $sql   = "SELECT * FROM saw_penilaian ORDER BY nama ASC";
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
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>Data tidak ada</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- NORMALISASI -->
                    <div>
                        <h6><b>NORMALISASI</b></h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Smartphone</th>
                                    <th>Performa & Ulasan</th>
                                    <th>Harga (cost)</th>
                                    <th>Kapasitas Baterai</th>
                                    <th>Kamera</th>
                                    <th>Fitur Pendukung</th>
                                    <th>Ketersediaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Cari nilai max/min tiap kriteria
                                $C1 = $C2 = $C3 = $C4 = $C5 = $C6 = 0;

                                $sql = "SELECT * FROM saw_penilaian";
                                $hasil = $conn->query($sql);
                                if ($hasil && $hasil->num_rows > 0) {
                                    // C1 (benefit) -> max
                                    $sqlMax = "SELECT * FROM saw_penilaian ORDER BY peringkat DESC LIMIT 1";
                                    $rowMax = $conn->query($sqlMax)->fetch_row();
                                    $C1 = $rowMax[1];

                                    // C2 (cost) -> min
                                    $sqlMin = "SELECT * FROM saw_penilaian ORDER BY ukuran ASC LIMIT 1";
                                    $rowMin = $conn->query($sqlMin)->fetch_row();
                                    $C2 = $rowMin[2];

                                    // C3, C4, C5, C6 (benefit) -> max
                                    $C3 = $conn->query("SELECT * FROM saw_penilaian ORDER BY unduhan DESC LIMIT 1")->fetch_row()[3];
                                    $C4 = $conn->query("SELECT * FROM saw_penilaian ORDER BY aktif DESC LIMIT 1")->fetch_row()[4];
                                    $C5 = $conn->query("SELECT * FROM saw_penilaian ORDER BY manfaat DESC LIMIT 1")->fetch_row()[5];
                                    $C6 = $conn->query("SELECT * FROM saw_penilaian ORDER BY kelebihan DESC LIMIT 1")->fetch_row()[6];

                                    $b = 0;
                                    $hasil2 = $conn->query("SELECT * FROM saw_penilaian ORDER BY nama ASC");
                                    while ($row = $hasil2->fetch_row()) {
                                        ?>
                                        <tr>
                                            <td align="center"><?php echo ++$b; ?></td>
                                            <td><?= htmlspecialchars($row[0]); ?></td>
                                            <td align="center"><?= round($row[1] / $C1, 2); ?></td>
                                            <td align="center"><?= round($C2 / $row[2], 2); ?></td>
                                            <td align="center"><?= round($row[3] / $C3, 2); ?></td>
                                            <td align="center"><?= round($row[4] / $C4, 2); ?></td>
                                            <td align="center"><?= round($row[5] / $C5, 2); ?></td>
                                            <td align="center"><?= round($row[6] / $C6, 2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>Data tidak ada</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- NILAI PREFERENSI -->
                    <div>
                        <h6><b>NILAI PREFERENSI</b></h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Smartphone</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $b = 0;
                                $B1 = $B2 = $B3 = $B4 = $B5 = $B6 = 0;

                                // Ambil bobot dari saw_kriteria
                                $hasilK = $conn->query("SELECT * FROM saw_kriteria");
                                if ($hasilK && $hasilK->num_rows > 0) {
                                    $rowK = $hasilK->fetch_row();
                                    $B1 = $rowK[1];
                                    $B2 = $rowK[2];
                                    $B3 = $rowK[3];
                                    $B4 = $rowK[4];
                                    $B5 = $rowK[5];
                                    $B6 = $rowK[6];
                                }

                                // Kosongkan tabel perankingan
                                $conn->query("TRUNCATE TABLE saw_perankingan");

                                $sql = "SELECT * FROM saw_penilaian";
                                $hasil = $conn->query($sql);
                                if ($hasil && $hasil->num_rows > 0) {
                                    while ($row = $hasil->fetch_row()) {
                                        // C1, C3-6 benefit; C2 cost
                                        $nilai = round(
                                            (($row[1] / $C1) * $B1) +
                                            (($C2 / $row[2]) * $B2) +
                                            (($row[3] / $C3) * $B3) +
                                            (($row[4] / $C4) * $B4) +
                                            (($row[5] / $C5) * $B5) +
                                            (($row[6] / $C6) * $B6),
                                            3
                                        );
                                        $nama = $row[0];

                                        $stmt = $conn->prepare(
                                            "INSERT INTO saw_perankingan (nama, nilai_akhir) VALUES (?, ?)"
                                        );
                                        $stmt->bind_param("sd", $nama, $nilai);
                                        $stmt->execute();
                                    }
                                }

                                $hasilP = $conn->query("SELECT * FROM saw_perankingan");
                                if ($hasilP && $hasilP->num_rows > 0) {
                                    while ($row = $hasilP->fetch_row()) {
                                        ?>
                                        <tr>
                                            <td><?php echo ++$b; ?></td>
                                            <td><?= htmlspecialchars($row[1]); ?></td>
                                            <td><?= $row[2]; ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>Data tidak ada</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- PERANKINGAN -->
                    <div>
                        <h6><b>PERANKINGAN</b></h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Smartphone</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $b = 0;
                                $hasilR = $conn->query("SELECT * FROM saw_perankingan ORDER BY nilai_akhir DESC");
                                if ($hasilR && $hasilR->num_rows > 0) {
                                    while ($row = $hasilR->fetch_row()) {
                                        ?>
                                        <tr>
                                            <td><?php echo ++$b; ?></td>
                                            <td><?= htmlspecialchars($row[1]); ?></td>
                                            <td><?= $row[2]; ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>Data tidak ada</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

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
