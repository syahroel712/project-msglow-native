<?php
error_reporting(0);
include "../koneksi.php";
include "../tglindo.php";
$tgl = date('d M Y');
?>

<body onLoad="javascript:print()">
	<?php
	//include "../fungsi_indotgl.php" ;
	$BulanIndo = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

	$tgl = date("Y-m-d");
	$thn = date('Y');

	if ($_GET['th'] != "") {
		$where = " year(tgl_jual)='$_GET[th]'";
		$ket = "$_GET[th]";
	} else {
		$where = " year(tgl_jual)='$thn'";
		$ket = "$thn";
	}

	$setting = mysql_fetch_assoc(mysql_query("SELECT * FROM setting"));
	?>
	<h3 align="center" style="margin:0px"><?= $setting['nama_toko'] ?></h3>
	<h5 align='center' style="margin:0px"><?= $setting['alamat'] ?></h5>
	<h4 align="center" style="margin-top:0px">Laporan Keuangan (<?php echo "$ket"; ?>)</h4>


	<table id="example1" width="100%" cellspacing=0 border=1 align="center" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>No</th>
				<th>Bulan</th>
				<th>Penjualan</th>
				<th>Pengeluaran</th>
				<th>Kas</th>

			</tr>
		</thead>
		<tbody id="penjualan">
			<?php
			$sql = mysql_query("SELECT *,month(p.tgl_jual)as bln,sum(d.modal*d.jumlah_jual)as modal2,sum(d.harga_satuan*d.jumlah_jual)as tot from tb_penjualan p,tb_barang_terjual d where $where and p.no_nota=d.no_nota group by month(p.tgl_jual) order by month(p.tgl_jual) asc") or die(mysql_error());
			$cek = mysql_num_rows($sql);
			if ($cek < 1) {
			?><tr>
					<td colspan="13" style="padding:10px;">Data tidak ditemukan</td>
				</tr><?php
						} else {
							$no = 1;
							while ($data = mysql_fetch_array($sql)) {
								$pengeluaran = mysql_fetch_array(mysql_query("select *,sum(total)as tot from tb_pembelian where month(tgl_beli)='$data[bln]' and year(tgl_beli)='$ket' group by month(tgl_beli)"));
							?><tr>
						<td><?php echo $no++; ?></td>
						<td><?php echo $BulanIndo[$data['bln']]; ?></td>
						<td align="right"><?php echo number_format($data['tot'], 2, ".", ","); ?></td>
						<td align="right"><?php echo number_format($pengeluaran['tot'], 2, ".", ","); ?></td>
						<td align="right"><?php echo number_format(($data['tot'] - $pengeluaran['tot']), 2, ".", ","); ?></td>
					</tr><?php
								$tot1 = $tot1 + $pengeluaran['tot'];
								$tot2 = $tot2 + $data['tot'];
							}
							echo "
			<tr>
				<td colspan=2><b>TOTAL</b></td>
				<td align='right'><b>" . number_format($tot2, 2) . "</b></td>
				<td align='right'><b>" . number_format($tot1, 2) . "</b></td>
				<td align='right'><b>" . number_format($tot2 - $tot1, 2) . "</b></td>
			</tr>
			";
						} ?>
		</tbody>
	</table>
</body>