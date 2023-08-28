<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding"><label class="control-label text-left">Tanggal</label></div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date" id="tanggal">
	        <input type="text" class="form-control text-center" data-required="1" placeholder="Start Date" data-tgl="<?php echo $data['tanggal']; ?>" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>
<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding"><label class="control-label text-left">Transaksi</label></div>
	<div class="col-xs-12 no-padding">
		<select class="form-control jurnal_trans" data-required="1">
			<option value="">-- Pilih --</option>
			<?php foreach ($jurnal_trans as $k_jt => $v_jt): ?>
				<?php
					$selected = null;
					if ( $v_jt['id'] == $data['jurnal_trans_id'] ) {
						$selected = 'selected';
					}
				?>
				<option value="<?php echo $v_jt['id']; ?>" <?php echo $selected; ?> > <?php echo strtoupper($v_jt['nama']); ?> </option>
			<?php endforeach ?>
		</select>
	</div>
</div>
<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="text-center col-xs-11">Detail Transaksi</th>
					<th class="text-center col-xs-1"></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td style="padding: 10px;">
							<div class="col-xs-12 no-padding">
								<div class="col-xs-12 no-padding"><label class="control-label" style="padding-top: 0px;">Tanggal</label></div>
								<div class="col-xs-12 no-padding">
									<div class="input-group date" id="tgl_trans">
								        <input type="text" class="form-control text-center" data-required="1" placeholder="Tanggal" data-tgl="<?php echo $v_det['tanggal']; ?>" />
								        <span class="input-group-addon">
								            <span class="glyphicon glyphicon-calendar"></span>
								        </span>
								    </div>
								</div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-6 no-padding" style="padding-right: 5px;">
									<div class="col-xs-12 no-padding"><label class="control-label">Detail Transaksi</label></div>
									<div class="col-xs-12 no-padding">
										<select class="form-control jurnal_trans_detail" data-required="1">
											<option value="">-- Pilih --</option>
											<?php foreach ($jurnal_trans as $k_jt => $v_jt): ?>
												<?php foreach ($v_jt['detail'] as $k_jtd => $v_jtd): ?>
													<?php
														$selected = null;
														if ( $v_jtd['id'] == $v_det['det_jurnal_trans_id'] ) {
															$selected = 'selected';
														}
													?>
													<option value="<?php echo $v_jtd['id']; ?>" data-idheader="<?php echo $v_jt['id']; ?>" <?php echo $selected; ?> > <?php echo strtoupper($v_jtd['nama']); ?> </option>
												<?php endforeach ?>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="col-xs-6 no-padding" style="padding-left: 5px;">
									<div class="col-xs-12 no-padding"><label class="control-label">Perusahaan</label></div>
									<div class="col-xs-12 no-padding">
										<select class="form-control perusahaan" data-required="1">
											<option value="">-- Pilih --</option>
											<?php foreach ($perusahaan as $k_prs => $v_prs): ?>
												<?php
													$selected = null;
													if ( $v_prs['kode'] == $v_det['perusahaan'] ) {
														$selected = 'selected';
													}
												?>
												<option value="<?php echo $v_prs['kode']; ?>" <?php echo $selected; ?> > <?php echo strtoupper($v_prs['nama']); ?> </option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-1 no-padding"><label class="control-label">Rek Asal</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<div class="col-xs-10 no-padding sumber_coa" data-coa="<?php echo $v_det['coa_asal'] ?>"><label class="control-label"><?php echo $v_det['asal']; ?></label></div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-1 no-padding"><label class="control-label">Rek Tujuan</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<div class="col-xs-10 no-padding tujuan_coa" data-coa="<?php echo $v_det['coa_tujuan'] ?>"><label class="control-label"><?php echo $v_det['tujuan']; ?></label></div>
							</div>
							<div class="col-xs-12 no-padding">
							<div class="col-xs-12 no-padding">
								<div class="col-xs-12 no-padding"><label class="control-label" style="color: red;">Supplier (Isi untuk transaksi CN)</label></div>
								<div class="col-xs-12 no-padding">
									<select class="form-control supplier">
										<option value="">-- Pilih Supplier --</option>
										<?php foreach ($supplier as $k_supl => $v_supl): ?>
											<?php
												$selected = null;
												if ( $v_supl['nomor'] == $v_det['supplier'] ) {
													$selected = 'selected';
												}
											?>
											<option value="<?php echo $v_supl['nomor']; ?>" <?php echo $selected; ?> > <?php echo strtoupper($v_supl['nama']); ?> </option>
										<?php endforeach ?>
									</select>
								</div>
							</div>
						</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-6 no-padding" style="padding-right: 5px;">
									<div class="col-xs-12 no-padding"><label class="control-label">Unit</label></div>
									<div class="col-xs-12 no-padding">
										<select class="form-control unit" data-required="1">
											<option value="">-- Pilih --</option>
											<option value="all" <?php echo ($v_det['unit'] == 'all') ? 'selected' : null; ?> >ALL</option>
											<option value="pusat" <?php echo ($v_det['unit'] == 'pusat') ? 'selected' : null; ?> >PUSAT</option>
											<?php foreach ($unit as $k_unit => $v_unit): ?>
												<?php
													$selected = null;
													if ( $v_unit['kode'] == $v_det['unit'] ) {
														$selected = 'selected';
													}
												?>
												<option value="<?php echo $v_unit['kode']; ?>" <?php echo $selected; ?> ><?php echo $v_unit['nama']; ?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="col-xs-6 no-padding" style="padding-left: 5px;">
									<div class="col-xs-12 no-padding"><label class="control-label">Nominal</label></div>
									<div class="col-xs-12 no-padding">
										<input type="text" class="form-control text-right nominal" data-tipe="decimal" maxlength="20" placeholder="Nominal" data-required="1" value="<?php echo angkaDecimal($v_det['nominal']); ?>">
									</div>
								</div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-12 no-padding"><label class="control-label">Keterangan</label></div>
								<div class="col-xs-12 no-padding">
									<textarea class="form-control keterangan" data-required="1" placeholder="Keterangan">
										<?php echo $v_det['keterangan']; ?>
									</textarea>
								</div>
							</div>
						</td>
						<td class="col-xs-1">
							<div class="col-xs-6 text-center no-padding">
								<button type="button" class="btn btn-primary" onclick="jp.addRow(this)"><i class="fa fa-plus"></i></button>
							</div>
							<div class="col-xs-6 text-center no-padding">
								<button type="button" class="btn btn-danger" onclick="jp.removeRow(this)"><i class="fa fa-times"></i></button>
							</div>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>
<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="jp.edit(this)" data-id="<?php echo $data['id']; ?>"><i class="fa fa-save"></i> Simpan Perubahan</button>
</div>