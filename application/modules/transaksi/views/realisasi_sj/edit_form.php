<div class="form-group">
	<div class="col-md-12">
		<label class="control-label"><u>DATA SJ</u></label>
	</div>
</div>
<div class="form-group">
	<div class="col-md-12">
		<small>
			<table class="table table-bordered tbl_list_plg" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-md-2 text-center" rowspan="2">Nama Pelanggan</th>
						<th class="col-md-1 text-center" rowspan="2">No. DO</th>
						<th class="col-md-1 text-center" rowspan="2">No. SJ</th>
						<th class="text-center" colspan="4">Pengajuan</th>
						<!-- <th class="text-center" colspan="4">Realisasi</th> -->
					</tr>
					<tr>
						<th class="col-md-1 text-center">Ekor</th>
						<th class="col-md-1 text-center">Tonase</th>
						<th class="col-md-1 text-center">BB</th>
						<th class="col-md-1 text-center">Harga</th>
						<!-- <th class="col-md-1 text-center">Ekor</th>
						<th class="col-md-1 text-center">Tonase</th>
						<th class="col-md-1 text-center">BB</th>
						<th class="col-md-1 text-center">Harga</th> -->
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data_penjualan) ): ?>
						<?php foreach ($data_penjualan as $k_dp => $v_dp): ?>
							<?php foreach ($v_dp['det_rpah_real_sj'] as $k => $val): ?>
								<?php if ( $val['noreg'] == $noreg ): ?>
									<tr class="v-center data" data-id="<?php echo $val['id']; ?>">
										<td class="pelanggan" data-nomor="<?php echo $val['no_pelanggan']; ?>"><?php echo $val['pelanggan']; ?></td>
										<td class="text-center no_do"><?php echo $val['no_do']; ?></td>
										<td class="text-center no_sj"><?php echo $val['no_sj']; ?></td>
										<td class="text-right"><?php echo angkaRibuan($val['ekor']); ?></td>
										<td class="text-right"><?php echo angkaDecimal($val['tonase']); ?></td>
										<td class="text-right"><?php echo angkaDecimal($val['bb']); ?></td>
										<td class="text-right"><?php echo angkaRibuan($val['harga']); ?></td>
										<!-- <td class="text-right">
											<input type="text" class="form-control ekor text-right" data-tipe="integer" value="<?php echo angkaRibuan($val['data_real_sj']['ekor']); ?>" onblur="real_sj.hit_bb(this)">
										</td>
										<td class="text-right">
											<input type="text" class="form-control tonase text-right" data-tipe="decimal" value="<?php echo angkaDecimal($val['data_real_sj']['tonase']); ?>" onblur="real_sj.hit_bb(this)">
										</td>
										<td class="text-right">
											<input type="text" class="form-control bb text-right" data-tipe="decimal" value="<?php echo angkaDecimal($val['data_real_sj']['bb']); ?>" readonly>
										</td>
										<td class="text-right">
											<input type="text" class="form-control harga text-right" data-tipe="integer" value="<?php echo angkaRibuan($val['data_real_sj']['harga']); ?>">
										</td> -->
									</tr>
									<tr class="realisasi">
										<td colspan="7" style="background-color: #eeeeee;">
											<table class="table table-bordered" style="margin-bottom: 0px;">
												<thead>
													<tr>
														<th class="text-center">Ekor</th>
														<th class="text-center">Tonase</th>
														<th class="text-center">BB</th>
														<th class="text-center">Harga</th>
														<th class="text-center">Jenis Ayam</th>
														<th class="text-center"></th>
													</tr>
												</thead>
												<tbody>
													<?php if ( !empty($val['data_real_sj']) ): ?>
														<?php foreach ($val['data_real_sj'] as $k_drs => $v_drs): ?>
															<?php if ( $v_drs['id_header'] == $data['id'] ): ?>
																<tr data-id="<?php echo $v_drs['id']; ?>">
																	<td class="text-right">
																		<input type="text" class="form-control ekor text-right" placeholder="Ekor" data-tipe="integer" value="<?php echo angkaRibuan($v_drs['ekor']); ?>" onblur="real_sj.hit_bb(this)">
																	</td>
																	<td class="text-right">
																		<input type="text" class="form-control tonase text-right" placeholder="Kg" data-tipe="decimal" value="<?php echo angkaDecimal($v_drs['tonase']); ?>" onblur="real_sj.hit_bb(this)">
																	</td>
																	<td class="text-right">
																		<input type="text" class="form-control bb text-right" placeholder="BB" data-tipe="decimal" value="<?php echo angkaDecimal($v_drs['bb']); ?>" readonly>
																	</td>
																	<td class="text-right">
																		<input type="text" class="form-control harga text-right" placeholder="Harga" data-tipe="integer" value="<?php echo angkaRibuan($v_drs['harga']); ?>">
																	</td>
																	<td class="text-right">
																		<select class="form-control jenis_ayam" data-required="1">
																			<option value="">Pilih Jenis</option>
																			<?php foreach ($jenis_ayam as $k_ja => $v_ja): ?>
																				<?php 
																					$selected = null;
																					if ( $v_drs['jenis_ayam'] == $k_ja ) {
																						$selected = 'selected';
																					}
																				?>
																				<option value="<?php echo $k_ja ?>" <?php echo $selected; ?> ><?php echo strtoupper($v_ja); ?></option>
																				}
																			<?php endforeach ?>
																		</select>
																	</td>
																	<td>
																		<div class="col-sm-12 no-padding">
																			<div class="col-sm-6" style="padding: 0px 2px 0px 0px;">
																				<button type="button" class="col-sm-12 btn btn-primary" onclick="real_sj.addRow(this)"><i class="fa fa-plus"></i></button>
																			</div>
																			<div class="col-sm-6" style="padding: 0px 0px 0px 3px;">
																				<button type="button" class="col-sm-12 btn btn-danger" onclick="real_sj.removeRow(this)"><i class="fa fa-times"></i></button>
																			</div>
																		</div>
																	</td>
																</tr>
															<?php endif ?>
														<?php endforeach ?>
													<?php else: ?>
														<tr data-id="">
															<td class="text-right">
																<input type="text" class="form-control ekor text-right" placeholder="Ekor" data-tipe="integer" value="<?php echo angkaRibuan(0); ?>" onblur="real_sj.hit_bb(this)">
															</td>
															<td class="text-right">
																<input type="text" class="form-control tonase text-right" placeholder="Kg" data-tipe="decimal" value="<?php echo angkaDecimal(0); ?>" onblur="real_sj.hit_bb(this)">
															</td>
															<td class="text-right">
																<input type="text" class="form-control bb text-right" placeholder="BB" data-tipe="decimal" value="<?php echo angkaDecimal(0); ?>" readonly>
															</td>
															<td class="text-right">
																<input type="text" class="form-control harga text-right" placeholder="Harga" data-tipe="integer" value="<?php echo angkaRibuan(0); ?>">
															</td>
															<td class="text-right">
																<select class="form-control jenis_ayam" data-required="1">
																	<option value="">Pilih Jenis</option>
																	<?php foreach ($jenis_ayam as $k_ja => $v_ja): ?>
																		<?php 
																			$selected = null;
																			if ( $v_drs['jenis_ayam'] == $k_ja ) {
																				$selected = 'selected';
																			}
																		?>
																		<option value="<?php echo $k_ja ?>" <?php echo $selected; ?> ><?php echo strtoupper($v_ja); ?></option>
																		}
																	<?php endforeach ?>
																</select>
															</td>
															<td>
																<div class="col-sm-12 no-padding">
																	<div class="col-sm-6" style="padding: 0px 2px 0px 0px;">
																		<button type="button" class="col-sm-12 btn btn-primary" onclick="real_sj.addRow(this)"><i class="fa fa-plus"></i></button>
																	</div>
																	<div class="col-sm-6" style="padding: 0px 0px 0px 3px;">
																		<button type="button" class="col-sm-12 btn btn-danger" onclick="real_sj.removeRow(this)"><i class="fa fa-times"></i></button>
																	</div>
																</div>
															</td>
														</tr>
													<?php endif ?>
												</tbody>
											</table>
										</td>
									</tr>
								<?php endif ?>
							<?php endforeach ?>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="11">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>
<div class="form-group">
	<div class="col-md-1">
		<label class="control-label">Ekor</label>
	</div>
	<div class="col-md-2">
		<input type="text" class="form-control text-right tot_ekor" data-tipe="integer" placeholder="Ekor" data-required="1" value="<?php echo angkaRibuan($data['ekor']); ?>" onblur="real_sj.hit_bb()" readonly>
	</div>
	<div class="col-md-1">
		<label class="control-label">Kg</label>
	</div>
	<div class="col-md-2">
		<input type="text" class="form-control text-right tot_kg" data-tipe="decimal" placeholder="Kg" data-required="1" value="<?php echo angkaDecimal($data['kg']); ?>" onblur="real_sj.hit_bb()" readonly>
	</div>
	<div class="col-md-1">
		<label class="control-label">BB</label>
	</div>
	<div class="col-md-1">
		<input type="text" class="form-control text-right tot_bb" data-tipe="decimal" placeholder="BB" data-required="1" value="<?php echo angkaDecimal($data['kg']); ?>" readonly>
	</div>
	<div class="col-md-1">
		<label class="control-label">Tara</label>
	</div>
	<div class="col-md-2">
		<input type="text" class="form-control text-right tara" data-tipe="decimal" placeholder="Tara Keranjang" data-required="1" value="<?php echo angkaDecimal($data['tara']); ?>" onblur="real_sj.hit_total()">
	</div>
</div>
<div class="form-group">
	<div class="col-md-12">
		<hr style="margin-top: 0px; margin-bottom: 0px;">
	</div>
</div>
<div class="form-group">
	<div class="col-md-1" style="padding-right: 0px;">
		<label class="control-label">Netto Ekor</label>
	</div>
	<div class="col-md-2">
		<input type="text" class="form-control text-right netto_ekor" data-tipe="integer" placeholder="Netto Ekor" data-required="1" value="<?php echo angkaRibuan($data['netto_ekor']); ?>" readonly>
	</div>
	<div class="col-md-1">
		<label class="control-label">Netto Kg</label>
	</div>
	<div class="col-md-2">
		<input type="text" class="form-control text-right netto_kg" data-tipe="decimal" placeholder="Netto Kg" data-required="1" value="<?php echo angkaDecimal($data['netto_kg']); ?>" readonly>
	</div>
	<div class="col-md-1">
		<label class="control-label">BB Netto</label>
	</div>
	<div class="col-md-1">
		<input type="text" class="form-control text-right netto_bb" data-tipe="decimal" placeholder="Netto BB" data-required="1" value="<?php echo angkaDecimal($data['netto_bb']); ?>" readonly>
	</div>
</div>
<div class="form-group">
	<div class="col-md-12">
		<hr style="margin-top: 0px; margin-bottom: 0px;">
	</div>
</div>
<div class="form-group">
	<div class="col-md-12">
    	<button type="button" class="btn btn-danger" onclick="real_sj.get_data(this)" style="margin-right: 10px;"><i class="fa fa-times"></i> Batal</button>
		<button type="button" class="btn btn-primary" onclick="real_sj.edit(this);" data-id="<?php echo $data['id']; ?>"><i class="fa fa-save"></i> Edit</button>
	</div>
</div>