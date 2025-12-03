<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="active">
  <h1><a href="index.php" class="logo">SP</a></h1>
  <ul class="list-unstyled components mb-5">
    <li class="<?php echo $current === 'index.php' ? 'active' : ''; ?>">
      <a href="index.php"><span class="fa fa-mobile"></span> Alternatif Smartphone</a>
    </li>
    <li class="<?php echo $current === 'kriteria.php' ? 'active' : ''; ?>">
      <a href="kriteria.php"><span class="fa fa-sliders"></span> Kriteria</a>
    </li>
    <li class="<?php echo $current === 'penilaian.php' ? 'active' : ''; ?>">
      <a href="penilaian.php"><span class="fa fa-list-ol"></span> Penilaian</a>
    </li>
    <li class="<?php echo $current === 'hitung.php' ? 'active' : ''; ?>">
      <a href="hitung.php"><span class="fa fa-cogs"></span> Hitung SAW</a>
    </li>
  </ul>
</nav>
