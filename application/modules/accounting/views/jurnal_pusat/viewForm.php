<div class="col-xs-12 no-padding">
	<div class="col-xs-1 no-padding"><label class="control-label">Tanggal</label></div>
	<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
	<div class="col-xs-10 no-padding"><label class="control-label"><?php echo strtoupper(tglIndonesia($data['tanggal'], '-', ' ')); ?></label></div>
</div>
<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-1 no-padding"><label class="control-label">Transaksi</label></div>
	<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
	<div class="col-xs-10 no-padding"><label class="control-label"><?php echo strtoupper($data['jurnal_trans']['nama']); ?></label></div>
</div>
<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="text-center col-xs-12">Detail Transaksi</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td style="padding: 10px;">
							<div class="col-xs-12 no-padding">
								<div class="col-xs-2 no-padding"><label class="control-label" style="padding-top: 0px;">Tanggal</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label" style="padding-top: 0px;">:</label></div>
								<div class="col-xs-9 no-padding"><label class="control-label" style="padding-top: 0px;"><?php echo strtoupper(tglIndonesia($v_det['tanggal'], '-', ' ')); ?></label></div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-2 no-padding"><label class="control-label">Detail Transaksi</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<div class="col-xs-9 no-padding"><label class="control-label"><?php echo strtoupper($v_det['jurnal_trans_detail']['nama']); ?></label></div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-2 no-padding"><label class="control-label">Perusahaan</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<div class="col-xs-9 no-padding"><label class="control-label"><?php echo strtoupper($v_det['d_perusahaan']['perusahaan']); ?></label></div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-2 no-padding"><label class="control-label">Rek Asal</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<div class="col-xs-9 no-padding sumber_coa"><label class="control-label"><?php echo strtoupper($v_det['asal']); ?></label></div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-2 no-padding"><label class="control-label">Rek Tujuan</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<div class="col-xs-9 no-padding tujuan_coa"><label class="control-label"><?php echo strtoupper($v_det['tujuan']); ?></label></div>
							</div>
							<?php if ( !empty($v_det['supplier']) ): ?>
								<div class="col-xs-12 no-padding">
									<div class="col-xs-2 no-padding"><label class="control-label" style="color: red;">Supplier</label></div>
									<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
									<div class="col-xs-9 no-padding tujuan_coa"><label class="control-label"><?php echo strtoupper($v_det['d_supplier']['nama']); ?></label></div>
								</div>
							<?php endif ?>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-2 no-padding"><label class="control-label">Unit</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<?php
									$unit = null;
									if ( !empty($v_det['d_unit']) ) {
										$unit = str_replace('kab ', '', $v_det['d_unit']['nama']);
						            	$unit = str_replace('kota ', '', $unit);
									} else {
										$unit = strtoupper($v_det['unit']);
									}
								?>
								<div class="col-xs-9 no-padding"><label class="control-label"><?php echo strtoupper($unit); ?></label></div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-2 no-padding"><label class="control-label">Nominal</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<div class="col-xs-9 no-padding"><label class="control-label"><?php echo angkaDecimal($v_det['nominal']); ?></label></div>
							</div>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-2 no-padding"><label class="control-label">Keterangan</label></div>
								<div class="col-xs-1 no-padding text-center" style="max-width: 2%;"><label class="control-label">:</label></div>
								<div class="col-xs-9 no-padding"><label class="control-label"><?php echo angkaDecimal($v_det['keterangan']); ?></label></label></div>
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
	<div class="col-xs-6 no-padding" style="padding-right: 5px;">
		<button type="button" class="col-xs-12 btn btn-danger pull-right" onclick="jp.delete(this)" data-id="<?php echo $data['id']; ?>"><i class="fa fa-trash"></i> Hapus</button>
	</div>
	<div class="col-xs-6 no-padding" style="padding-left: 5px;">
		<button type="button" class="col-xs-12 btn btn-primary pull-right" onclick="jp.changeTabActive(this)" data-id="<?php echo $data['id']; ?>" data-href="action" data-edit="edit"><i class="fa fa-edit"></i> Edit</button>
	</div>
</div>