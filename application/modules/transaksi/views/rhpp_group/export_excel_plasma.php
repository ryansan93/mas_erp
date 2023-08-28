<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
			.str { mso-number-format:\@; }
			.decimal_number_format { mso-number-format: "\#\,\#\#0.00"; }
			.number_format { mso-number-format: "\#\,\#\#0"; }
		</style>
	</head>
	<body>
		<div class="panel-body" style="margin-top: 0px; padding-top: 0px;">
		    <div class="row new-line">
		        <div class="col-sm-12">
		            <form class="form-horizontal" role="form">
		            	<b>
			            	<table style="width: 100%;">
			            		<tbody>
			            			<tr>
			            				<th style="width: 10%; text-align: left;">Mitra</th>
			            				<th class="str" style="width: 1%; text-align: left;">: <?php echo $data['mitra']; ?></th>
			            			</tr>
			            			<tr>
			            				<th style="width: 10%; text-align: left;">Total Populasi</th>
			            				<th class="number_format" style="width: 1%; text-align: left;">: <?php echo ($data['tot_populasi']); ?></th>
			            			</tr>
			            			<tr><th></th></tr>
			            			<tr>
			            				<th style="width: 10%; text-align: left; text-decoration: underline;">Noreg</th>
			            				<th style="width: 10%; text-align: left; text-decoration: underline;">Kandang</th>
			            				<th style="width: 10%; text-align: left; text-decoration: underline;">Populasi</th>
			            				<th style="width: 10%; text-align: left; text-decoration: underline;">Tanggal DOC In</th>
			            				<th style="width: 10%; text-align: left; text-decoration: underline;" colspan="3">Tanggal Tutup Siklus</th>
			            			</tr>
			            				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
			            					<tr>
				            					<th class="str" style="width: 10%; text-align: left;"><?php echo $v_det['noreg']; ?></th>
					            				<th class="str" style="width: 10%; text-align: left;"><?php echo $v_det['kandang']; ?></th>
					            				<th class="number_format" style="width: 10%; text-align: left;"><?php echo ($v_det['populasi']); ?></th>
					            				<th class="str" style="width: 10%; text-align: left;"><?php echo tglIndonesia($v_det['tgl_docin'], '-', ' ', true); ?></th>
					            				<th class="str" style="width: 10%; text-align: left;" colspan="3"><?php echo tglIndonesia($v_det['tgl_tutup'], '-', ' ', true); ?></th>
			            					</tr>
			            				<?php endforeach ?>
			            		</tbody>
			            	</table>
			            </b>
		            </form>
		            <?php
		            	$populasi = $data['tot_populasi'];
		            	$rata_umur_panen = $data['rata_umur_panen'];

		            	$total_nilai_doc = 0;
		            	$total_nilai_pakan = 0;
		            	$total_nilai_pemakaian = 0;

		            	$total_jumlah_pakan = 0;
		            ?>
		            <form class="form-horizontal" role="form">
		                <div class="form-group">
	                		<table>
	                			<tbody>
	                				<tr>
	                					<td>
	                						<table>
	                							<thead>
	                								<tr>
	                									<td></td>
	                								</tr>
	                							</thead>
	                						</table>
	                						<table border="1" style="">
						                		<thead>
						                			<tr>
						                				<th align="center">Tanggal</th>
						                				<th align="center">Nota / SJ</th>
						                				<th align="center">Barang</th>
						                				<th align="center">Box / Sak</th>
						                				<th align="center">Jumlah</th>
						                				<th align="center">Harga</th>
						                				<th align="center" style="width: 10%;">Total</th>
						                			</tr>
						                		</thead>
						                		<tbody>
						                			<tr class="head">
						                				<td colspan=7" align="left"><b>DOC</b></td>
						                			</tr>
						                			<?php if ( !empty($data_plasma['detail']['data_doc']) ): ?>
						                				<?php 
						                					$data_doc = $data_plasma['detail']['data_doc']['doc'];
						                					$data_vaksin = $data_plasma['detail']['data_doc']['vaksin'];
						                					$total_box = 0;
						                					$total_jumlah = 0;
						                					$total_nilai = 0;
						                				?>
						                				<?php if ( !empty($data_doc) ): ?>
						                					<?php foreach ($data_doc as $k_doc => $v_doc): ?>
									                			<tr>
									                				<td class="str" align="left"><?php echo substr(substr($v_doc['tgl_docin'], 0, 10), -2).'/'.substr(substr($v_doc['tgl_docin'], 0, 10), 5, 2).'/'.substr(substr($v_doc['tgl_docin'], 0, 10), 0, 4); ?></td>
									                				<td class="str"><?php echo $v_doc['sj']; ?></td>
									                				<td><?php echo $v_doc['barang']; ?></td>
									                				<td class="number_format" align="right"><?php echo ($v_doc['box']); ?></td>
									                				<td class="number_format" align="right"><?php echo ($v_doc['jumlah']); ?></td>
									                				<td class="number_format" align="right"><?php echo ($v_doc['harga']); ?></td>
									                				<td class="number_format" align="right"><?php echo ($v_doc['total']); ?></td>
									                			</tr>
									                			<?php
									                				$total_box += $v_doc['box'];
								                					$total_jumlah += $v_doc['jumlah'];
								                					$total_nilai += $v_doc['total'];
									                			?>
						                					<?php endforeach ?>
						                				<?php endif ?>
						                				<?php if ( !empty($data_vaksin) ): ?>
						                					<?php foreach ($data_vaksin as $k_vaksin => $v_vaksin): ?>
									                			<tr>
									                				<td colspan="2"></td>
									                				<td><?php echo $v_vaksin['barang']; ?></td>
									                				<td colspan="2"></td>
									                				<td class="number_format" align="right"><?php echo ($v_vaksin['harga']); ?></td>
									                				<td class="number_format" align="right"><?php echo ($v_vaksin['total']); ?></td>
									                			</tr>
									                			<?php
								                					$total_nilai += $v_vaksin['total'];
									                			?>
						                					<?php endforeach ?>
						                				<?php endif ?>
						                				<tr>
						                					<td colspan="3" class="str" align="right"><b>TOTAL</b></td>
						                					<td class="number_format" align="right"><b><?php echo ($total_box); ?></b></td>
						                					<td class="number_format" align="right"><b><?php echo ($total_jumlah) ?></b></td>
						                					<td class="number_format" align="right" colspan="2"><b><?php echo ($total_nilai); ?></b></td>
						                				</tr>
						                				<?php $total_nilai_doc = $total_nilai; ?>
						                			<?php endif ?>

						                			<?php $total_pemakaian_zak = 0; ?>
						                			<?php $total_pemakaian_jumlah = 0; ?>
						                			<?php $total_pemakaian_nilai = 0; ?>
						                			<tr class="head">
						                				<td colspan="7" align="left"><b>PAKAN</b></td>
						                			</tr>
						                			<?php if ( !empty($data_plasma['detail']['data_pakan']) ): ?>
						                				<?php 
						                					$data_pakan = $data_plasma['detail']['data_pakan']; 
						                					$total_zak = 0;
						                					$total_jumlah = 0;
						                					$total_nilai = 0;
						                				?>
						                				<?php foreach ($data_pakan as $k => $val): ?>
						                					<tr>
								                				<td class="str" align="left"><?php echo substr(substr($val['tanggal'], 0, 10), -2).'/'.substr(substr($val['tanggal'], 0, 10), 5, 2).'/'.substr(substr($val['tanggal'], 0, 10), 0, 4); ?></td>
								                				<td class="str"><?php echo empty($val['sj']) ? '-' : $val['sj']; ?></td>
								                				<td><?php echo $val['barang']; ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['zak']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['jumlah']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['harga']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['total']); ?></td>
								                			</tr>
								                			<?php
								                				$total_zak += $val['zak'];
							                					$total_jumlah += $val['jumlah'];
							                					$total_nilai += $val['total'];
								                			?>
								                			<?php $total_pemakaian_zak += $val['zak']; ?>
								                			<?php $total_pemakaian_jumlah += $val['jumlah']; ?>
								                			<?php $total_pemakaian_nilai += $val['total']; ?>
						                				<?php endforeach ?>
						                				<tr>
						                					<td colspan="3" class="str" align="right"><b>TOTAL PENGIRIMAN</b></td>
						                					<td class="number_format" align="right"><b><?php echo ($total_zak); ?></b></td>
						                					<td class="decimal_number_format" align="right"><b><?php echo ($total_jumlah) ?></b></td>
						                					<td class="number_format" align="right" colspan="2"><b><?php echo ($total_nilai); ?></b></td>
						                				</tr>
						                				<?php 
						                					$total_nilai_pakan = $total_nilai; 
						                					$total_jumlah_pakan = $total_jumlah;
						                				?>
						                			<?php endif ?>

						                			<tr class="head">
						                				<td colspan="7" align="left"><b>PINDAH PAKAN</b></td>
						                			</tr>
						                			<?php if ( !empty($data_plasma['detail']['data_pindah_pakan']) ): ?>
						                				<?php 
						                					$data_pindah_pakan = $data_plasma['detail']['data_pindah_pakan'];
						                					$total_zak = 0;
						                					$total_jumlah = 0;
						                					$total_nilai = 0;
						                				?>
						                				<?php foreach ($data_pindah_pakan as $k => $val): ?>
						                					<tr>
								                				<td class="str" align="left"><?php echo substr(substr($val['tanggal'], 0, 10), -2).'/'.substr(substr($val['tanggal'], 0, 10), 5, 2).'/'.substr(substr($val['tanggal'], 0, 10), 0, 4); ?></td>
								                				<td class="str"><?php echo $val['sj']; ?></td>
								                				<td><?php echo $val['barang']; ?></td>
								                				<?php $zak = ($val['jumlah'] > 0) ? $val['jumlah']/50 : 0; ?>
								                				<td class="number_format" align="right"><?php echo ($zak); ?></td>
								                				<td class="decimal_number_format" align="right"><?php echo ($val['jumlah']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['harga']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['total']); ?></td>
								                			</tr>
								                			<?php $total_zak += $zak; ?>
								                			<?php $total_nilai += $val['total']; ?>
								                			<?php $total_jumlah += $val['jumlah']; ?>

								                			<?php $total_pemakaian_zak -= $zak; ?>
															<?php $total_pemakaian_jumlah -= $val['jumlah']; ?>
															<?php $total_pemakaian_nilai -= $val['total']; ?>
						                				<?php endforeach ?>
						                				<tr>
						                					<td colspan="3" class="str" align="right"><b>TOTAL PINDAH PAKAN</b></td>
						                					<td class="number_format" align="right"><b><?php echo ($total_zak); ?></b></td>
						                					<td class="decimal_number_format" align="right"><b><?php echo ($total_jumlah) ?></b></td>
						                					<td class="number_format" align="right" colspan="2"><b><?php echo ($total_nilai); ?></b></td>
						                				</tr>
						                				<?php $total_nilai_pakan -= $total_nilai; ?>
						                				<?php $total_jumlah_pakan -= $total_jumlah; ?>
						                			<?php endif ?>

						                			<tr class="head">
						                				<td colspan="7" align="left"><b>RETUR PAKAN</b></td>
						                			</tr>
						                			<?php if ( !empty($data_plasma['detail']['data_retur_pakan']) ): ?>
						                				<?php 
						                					$data_retur_pakan = $data_plasma['detail']['data_retur_pakan'];
						                					$total_zak = 0;
						                					$total_jumlah = 0;
						                					$total_nilai = 0;
						                				?>
						                				<?php foreach ($data_retur_pakan as $k => $val): ?>
						                					<tr>
								                				<td class="str" align="left"><?php echo substr(substr($val['tanggal'], 0, 10), -2).'/'.substr(substr($val['tanggal'], 0, 10), 5, 2).'/'.substr(substr($val['tanggal'], 0, 10), 0, 4); ?></td>
								                				<td class="str"><?php echo $val['no_retur']; ?></td>
								                				<td><?php echo $val['barang']; ?></td>
								                				<?php $zak = ($val['jumlah'] > 0) ? $val['jumlah']/50 : 0; ?>
								                				<td class="number_format" align="right"><?php echo ($zak); ?></td>
								                				<td class="decimal_number_format" align="right"><?php echo ($val['jumlah']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['harga']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['total']); ?></td>
								                			</tr>
								                			<?php $total_zak += $zak; ?>
								                			<?php $total_nilai += $val['total']; ?>
								                			<?php $total_jumlah += $val['jumlah']; ?>

								                			<?php $total_pemakaian_zak -= $zak; ?>
															<?php $total_pemakaian_jumlah -= $val['jumlah']; ?>
															<?php $total_pemakaian_nilai -= $val['total']; ?>
						                				<?php endforeach ?>
						                				<tr>
						                					<td colspan="3" class="str" align="right"><b>TOTAL RETUR</b></td>
						                					<td class="number_format" align="right"><b><?php echo ($total_zak); ?></b></td>
						                					<td class="decimal_number_format" align="right"><b><?php echo ($total_jumlah) ?></b></td>
						                					<td class="number_format" align="right" colspan="2"><b><?php echo ($total_nilai); ?></b></td>
						                				</tr>
						                				<?php $total_nilai_pakan -= $total_nilai; ?>
						                				<?php $total_jumlah_pakan -= $total_jumlah; ?>
						                			<?php endif ?>
						                			<tr>
					                					<td colspan="3" class="str" align="right"><b>TOTAL PEMAKAIAN</b></td>
					                					<td class="number_format" align="right"><b><?php echo ($total_pemakaian_zak); ?></b></td>
					                					<td class="decimal_number_format" align="right"><b><?php echo ($total_pemakaian_jumlah) ?></b></td>
					                					<td class="number_format" align="right" colspan="2"><b><?php echo ($total_pemakaian_nilai); ?></b></td>
					                				</tr>

						                			<?php $total_pemakaian = 0; ?>
						                			<tr class="head">
						                				<td colspan="7" align="left"><b>OVK</b></td>
						                			</tr>
						                			<?php if ( !empty($data_plasma['detail']['data_voadip']) ): ?>
						                				<?php 
						                					$data_voadip = $data_plasma['detail']['data_voadip'];
						                					$total_nilai = 0;
						                				?>
						                				<?php foreach ($data_voadip as $k => $val): ?>
						                					<tr>
								                				<td class="str" align="left"><?php echo substr(substr($val['tanggal'], 0, 10), -2).'/'.substr(substr($val['tanggal'], 0, 10), 5, 2).'/'.substr(substr($val['tanggal'], 0, 10), 0, 4); ?></td>
								                				<td class="str"><?php echo $val['sj']; ?></td>
								                				<td colspan="2"><?php echo $val['barang']; ?></td>
								                				<td class="decimal_number_format" align="right"><?php echo ($val['jumlah']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['harga']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['total']); ?></td>
								                			</tr>
								                			<?php $total_nilai += $val['total']; ?>
						                				<?php endforeach ?>
						                				<tr>
						                					<td colspan="4" class="str" align="right"><b>TOTAL PENGIRIMAN</b></td>
						                					<td class="number_format" align="right" colspan="3"><b><?php echo ($total_nilai); ?></b></td>
						                				</tr>
						                				<?php $total_pemakaian += $total_nilai; ?>
						                			<?php endif ?>

						                			<tr class="head">
						                				<td colspan="7" align="left"><b>RETUR OVK</b></td>
						                			</tr>
						                			<?php if ( !empty($data_plasma['detail']['data_retur_voadip']) ): ?>
						                				<?php 
						                					$data_retur_voadip = $data_plasma['detail']['data_retur_voadip'];
						                					$total_nilai = 0;
						                				?>
						                				<?php foreach ($data_retur_voadip as $k => $val): ?>
						                					<tr>
								                				<td class="str" align="left"><?php echo substr(substr($val['tanggal'], 0, 10), -2).'/'.substr(substr($val['tanggal'], 0, 10), 5, 2).'/'.substr(substr($val['tanggal'], 0, 10), 0, 4); ?></td>
								                				<td class="str"><?php echo $val['no_retur']; ?></td>
								                				<td colspan="2"><?php echo $val['barang']; ?></td>
								                				<td class="decimal_number_format" align="right"><?php echo ($val['jumlah']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['harga']); ?></td>
								                				<td class="number_format" align="right"><?php echo ($val['total']); ?></td>
								                			</tr>
								                			<?php $total_nilai += $val['total']; ?>
						                				<?php endforeach ?>
						                				<tr>
						                					<td colspan="4" class="str" align="right"><b>TOTAL RETUR</b></td>
						                					<td class="number_format" align="right" colspan="3"><b><?php echo ($total_nilai); ?></b></td>
						                				</tr>
						                				<?php $total_pemakaian -= $total_nilai; ?>
						                			<?php endif ?>

						                			<tr>
					                					<td colspan="4" class="str" align="right"><b>TOTAL PEMAKAIAN</b></td>
					                					<td class="number_format" align="right" colspan="3"><b><?php echo ($total_pemakaian); ?></b></td>
					                				</tr>
					                				<?php $total_nilai_pemakaian = $total_pemakaian; ?>
						                		</tbody>
						                	</table>
	                					</td>
	                					<td></td>
	                					<td>
											<?php
												$total_ekor = 0;
												$total_tonase = 0;
												$total_nilai_kontrak = 0;
												$total_nilai_pasar = 0;
												$total_nilai_insentif = 0;

												$total_pembelian_sapronak = $total_nilai_doc + $total_nilai_pakan + $total_nilai_pemakaian;
												$bonus_kematian = 0;
												$bonus_insentif_fcr = 0;
								            ?>
								            <b>Penjualan Ayam Peternak</b>
								            <table>
												<thead>
													<tr>
														<th align="center" style="border: 1px solid black; border-width: thin;">Tanggal</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">DO</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Pembeli</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Ekor</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Tonase (Kg)</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">BB Rata2</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Kontrak</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Total</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Hrg Pasar</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Total</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Selisih</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Insentif</th>
														<th align="center" style="border: 1px solid black; border-width: thin;">Total</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$fcr = 0;
														$bb = 0;
														$deplesi = 0;
														$ip = 0;
														$selisih_fcr = 0;
														$bonus_fcr = 0;
													?>
													<?php if ( !empty($data_plasma['detail']['data_rpah']) ): ?>
														<?php $data_rpah = $data_plasma['detail']['data_rpah']; ?>
														<?php foreach ($data_rpah as $k => $val): ?>
															<tr>
																<td class="str" align="left" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo substr(substr($val['tanggal'], 0, 10), -2).'/'.substr(substr($val['tanggal'], 0, 10), 5, 2).'/'.substr(substr($val['tanggal'], 0, 10), 0, 4); ?></td>
																<td class="str" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo $val['do'] ?></td>
																<td style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo $val['pembeli'] ?></td>
																<td class="number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['ekor']); ?></td>
																<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['tonase']); ?></td>
																<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['bb']); ?></td>
																<td class="number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['hrg_kontrak']); ?></td>
																<td class="number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['total_kontrak']); ?></td>
																<td class="number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['hrg_pasar']); ?></td>
																<td class="number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['total_pasar']); ?></td>
																<td class="number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['selisih']); ?></td>
																<td class="number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['insentif']); ?></td>
																<td class="number_format" align="right" style="border: 1px solid black; border-width: thin; vertical-align: top;"><?php echo ($val['total_insentif']); ?></td>
															</tr>
															<?php
																$total_ekor += $val['ekor'];
																$total_tonase += $val['tonase'];
																$total_nilai_kontrak += $val['total_kontrak'];
																$total_nilai_pasar += $val['total_pasar'];
																$total_nilai_insentif += $val['total_insentif'];
															?>
														<?php endforeach ?>
														<tr>
															<td colspan="3" class="str" align="right" style="border: 1px solid black; border-width: thin;"><b>TOTAL</b></td>
															<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_ekor); ?></b></td>
															<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_tonase); ?></b></td>
															<td colspan="2" style="border: 1px solid black; border-width: thin;"></td>
															<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_nilai_kontrak); ?></b></td>
															<td style="border: 1px solid black; border-width: thin;"></td>
															<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_nilai_pasar); ?></b></td>
															<td colspan="2" style="border: 1px solid black; border-width: thin;"></td>
															<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_nilai_insentif); ?></b></td>
														</tr>
														<?php 
															$fcr = $data['fcr'];
															$bb = $data['bb'];
															$deplesi = $data['deplesi'];
															$ip = $data['ip'];
														?>
														<?php 
															$bonus_kematian = ($deplesi <= 5) ? 25 * $total_tonase : 0;
															$bonus_insentif_fcr = $data['bonus_insentif_fcr']; 
														?>
													<?php else: ?>
														<tr align="center" colspan="13" style="border: 1px solid black; border-width: thin;">Data tidak ditemukan.</tr>
													<?php endif ?>
													<tr><td border="0"></td></tr>
													<?php
														$total_bonus_insentif_listrik = $data['total_bonus_insentif_listrik'];

										            	$total_pemasukan = $total_nilai_kontrak + $total_nilai_insentif + $bonus_kematian + $bonus_insentif_fcr + $total_bonus_insentif_listrik;
										            	$total_pengeluaran = $total_pembelian_sapronak + $data['biaya_materai'];
										            ?>
													<tr>
														<td colspan="2"><b>Rekapitulasi Peternak</b></td>
														<td colspan="3"></td>
														<td colspan="2"><b>Performance Peternak</b></td>
													</tr>
													<tr>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Penjualan Ayam</td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($total_nilai_kontrak); ?></td>
														<td class="str" align="right" style="border: 1px solid black; border-width: thin;">-</td>
														<td></td>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Jumlah Panen (Ekor)</td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($total_ekor); ?></td>
													</tr>
													<tr>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Total Pembelian Sapronak</td>
														<td class="str" align="right" style="border: 1px solid black; border-width: thin;">-</td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($total_pembelian_sapronak); ?></td>
														<td></td>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Berat Badan (Kg)</td>
														<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($total_tonase); ?></td>
													</tr>
													<tr>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Biaya Materai</td>
														<td class="str" align="right" style="border: 1px solid black; border-width: thin;">-</td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($data['biaya_materai']); ?></td>
														<td></td>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">BB Rata-Rata / Ekor (Kg)</td>
														<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($bb); ?></td>
													</tr>
													<tr>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Bonus Pasar 35%</td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($total_nilai_insentif); ?></td>
														<td class="str" align="right" style="border: 1px solid black; border-width: thin;">-</td>
														<td></td>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">FCR</td>
														<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($fcr); ?></td>
													</tr>
													<tr>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Bonus Kematian</td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($bonus_kematian); ?></td>
														<td class="str" align="right" style="border: 1px solid black; border-width: thin;">-</td>
														<td></td>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Deplesi</td>
														<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($deplesi); ?></td>
													</tr>
													<tr>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Bonus Insentif FCR</td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($bonus_insentif_fcr); ?></td>
														<td class="str" align="right" style="border: 1px solid black; border-width: thin;">-</td>
														<td></td>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Rata-Rata Umur</td>
														<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($rata_umur_panen); ?></td>
													</tr>
													<tr>
														<td colspan="2" style="border: 1px solid black; border-width: thin;">Bonus Insentif Listrik</td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($total_bonus_insentif_listrik); ?></td>
														<td class="str" align="right" style="border: 1px solid black; border-width: thin;">-</td>
														<td></td>
														<td colspan="2" style="border: 1px solid black; border-width: thin;"><b>IP</b></td>
														<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($ip); ?></b></td>
													</tr>
													<tr>
														<td colspan="2" style="border: 1px solid black; border-width: thin;"><b>TOTAL</b></td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_pemasukan); ?></b></td>
														<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_pengeluaran); ?></b></td>
													</tr>
													<tr><td border="0"></td></tr>
													<tr>
														<td colspan="2"><b>Potongan Peternak</b></td>
														<td colspan="3"></td>
														<td colspan="2"><b>Bonus Tambahan Peternak</b></td>
													</tr>
													<?php $total_potongan = 0; $total_bonus = 0; $tampil_total_potongan = 0;  $tampil_total_bonus = 0; ?>
													<?php $jml_row_potongan = 0; $jml_row_bonus = 0; ?>
													<?php if ( !empty($data_plasma['detail']['data_potongan']) || !empty($data_plasma['detail']['data_bonus']) ): ?>
														<?php if ( !empty($data_plasma['detail']['data_potongan']) ): ?>
															<?php $jml_row_potongan = count($data_plasma['detail']['data_potongan']); ?>
														<?php endif ?>
														<?php if ( !empty($data_plasma['detail']['data_bonus']) ): ?>
															<?php $jml_row_bonus = count($data_plasma['detail']['data_bonus']); ?>
														<?php endif ?>

														<?php $jml_row = ( $jml_row_potongan > $jml_row_bonus ) ? $jml_row_potongan : $jml_row_bonus; ?>
														<?php for ($i=0; $i <= $jml_row; $i++) { ?>
															<?php $data_potongan = $data_plasma['detail']['data_potongan']; ?>
															<?php $data_bonus = $data_plasma['detail']['data_bonus']; ?>
															<tr>
																<?php if ( isset($data_potongan[$i]) ) { ?>
																	<td colspan="2" style="border: 1px solid black; border-width: thin;"><?php echo $data_potongan[$i]['keterangan']; ?></td>
																	<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($data_potongan[$i]['sudah_bayar']); ?></td>

																	<?php $total_potongan += $data_potongan[$i]['sudah_bayar']; ?>
																<?php } else { ?>
																	<?php if ( $tampil_total_potongan == 0 ) { ?>
																		<td colspan="2" align="right" style="border: 1px solid black; border-width: thin;"><b>Total</b></td>
																		<td class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_potongan); ?></b></td>
																	<?php } else { ?>
																		<td colspan="2" align="right"><b></b></td>
																		<td class="str" align="right"><b></b></td>
																	<?php } ?>

																	<?php $tampil_total_potongan = 1; ?>
																<?php } ?>
																<td colspan="2"></td>
																<?php if ( isset($data_bonus[$i]) ) { ?>
																	<td colspan="5" style="border: 1px solid black; border-width: thin;"><?php echo $data_bonus[$i]['keterangan']; ?></td>
																	<td colspan="3" class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($data_bonus[$i]['jumlah']); ?></td>

																	<?php $total_bonus += $data_bonus[$i]['jumlah']; ?>
																<?php } else { ?>
																	<?php if ( $tampil_total_bonus == 0 ) { ?>
																		<td colspan="5" align="right" style="border: 1px solid black; border-width: thin;"><b>Total</b></td>
																		<td colspan="3" class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><b><?php echo ($total_bonus); ?></b></td>
																	<?php } else { ?>
																		<td colspan="5" align="right"><b></b></td>
																		<td colspan="3" class="str" align="right"><b></b></td>
																	<?php } ?>

																	<?php $tampil_total_bonus = 1; ?>
																<?php } ?>
															</tr>
														<?php } ?>
														<?php
															$total_pemasukan += $total_bonus;
															$total_pengeluaran += $total_potongan;
														?>
														<!-- <tr>
															<td colspan="2" align="right" style="border: 1px solid black; border-width: thin;">Total</td>
															<td class="decimal_number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($total_potongan); ?></td>
															<td colspan="2"></td>
															<td colspan="5" style="border: 1px solid black; border-width: thin;">Total</td>
															<td colspan="3" class="number_format" align="right" style="border: 1px solid black; border-width: thin;"><?php echo ($total_bonus); ?></td>
														</tr> -->
													<?php endif ?>

												</tbody>
											</table>
								            <br>
								            <table>
							            		<tbody>
							            			<tr>
							            				<td colspan="3"><b>Pendapatan Peternak Sebelum Pajak</b></td>
							            				<?php $pendapatan_peternak = $total_pemasukan - $total_pengeluaran; ?>
							            				<td class="decimal_number_format" align="right"><b><?php echo ($pendapatan_peternak); ?></b></td>
							            			</tr>
							            			<?php $nilai_potongan_pajak = ($data['potongan_pajak'] > 0) ? ($total_pemasukan - $total_pengeluaran) * ($data['potongan_pajak']/100) : 0; ?>
							            			<tr>
							            				<td colspan="3">Potongan Pajak <span class="prs_pajak"><?php echo angkaDecimal($data['potongan_pajak']); ?>%</span> (PPh Pasal 23)</td>
							            				<td class="decimal_number_format" align="right"><?php echo ( $nilai_potongan_pajak ); ?></td>
							            			</tr>
							            			<tr>
							            				<td colspan="3"><b>Pendapatan Peternak Setelah Kena Pajak</b></td>
							            				<td class="decimal_number_format" align="right"><b><?php echo ( ($total_pemasukan - $total_pengeluaran) - $nilai_potongan_pajak ); ?></b></td>
							            			</tr>
							            		</tbody>
							            	</table>
							            	<br><br>
								            <table>
							            		<tbody>
							            			<tr>
							            				<td colspan="2" align="center">Dibuat,</td>
							            				<td colspan="2" align="center">Peternak,</td>
							            				<td colspan="3" align="center">Mengetahui,</td>
							            			</tr>
							            			<tr><td></td></tr>
							            			<tr><td></td></tr>
							            			<tr><td></td></tr>
							            			<tr><td></td></tr>
							            			<tr>
							            				<td colspan="2" align="center"><b><?php echo strtoupper($data['user_cetak']); ?></b></td>
														<td colspan="2" align="center"><b><?php echo strtoupper($data['mitra']); ?></b></td>
														<td colspan="3" align="center"><b><?php echo strtoupper($data['kanit']); ?></b></td>
							            			</tr>
							            		</tbody>
							            	</table>
	                					</td>
	                				</tr>
	                			</tbody>
	                		</table>
		                </div>
		            </form>
		        </div>
		    </div>
		</div>
	</body>
</html>