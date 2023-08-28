<form class="form-horizontal">
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-2 no-padding"><label class="control-label text-left">Tanggal Bayar</label></div>
			<div class="col-xs-10 no-padding">
				<label class="control-label text-left"><?php echo ': '.strtoupper(tglIndonesia($data['tgl_bayar'], '-', ' ', 'true')); ?></label>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-2 no-padding"><label class="control-label text-left">No. Bayar</label></div>
			<div class="col-xs-10 no-padding">
				<label class="control-label text-left"><?php echo ': '.$data['no_bayar']; ?></label>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-2 no-padding"><label class="control-label text-left">Jumlah Transfer</label></div>
			<div class="col-xs-10 no-padding">
				<label class="control-label text-left"><?php echo ': '.angkaDecimal($data['jml_transfer']); ?></label>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-2 no-padding"><label class="control-label text-left">Jumlah Potongan</label></div>
			<div class="col-xs-10 no-padding">
				<label class="control-label text-left"><?php echo ': '.angkaDecimal($data['total_potongan']); ?></label>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-2 no-padding"><label class="control-label text-left">Jumlah CN</label></div>
			<div class="col-xs-10 no-padding">
				<label class="control-label text-left"><?php echo ': '.angkaDecimal($data['cn']); ?></label>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-2 no-padding"><label class="control-label text-left">Total Bayar</label></div>
			<div class="col-xs-10 no-padding">
				<label class="control-label text-left"><?php echo ': '.angkaDecimal($data['jumlah_bayar']); ?></label>
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding" style="margin-bottom: 5px; padding: 0px 5px 0px 0px;">
		<div class="col-xs-2 no-padding"><label class="control-label text-left">Jenis Pembayaran</label></div>
		<div class="col-xs-10 no-padding">
			<label class="control-label text-left"><?php echo ': '.strtoupper($data['jenis_pembayaran']); ?></label>
		</div>
	</div>
	<div class="col-xs-12 search left-inner-addon no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
	<div class="col-xs-12 no-padding" style="margin-bottom: 5px; padding: 0px 5px 0px 0px;">
		<div class="col-xs-2 no-padding"><label class="control-label text-left">Jenis Transaksi</label></div>
		<div class="col-xs-10 no-padding">
			<label class="control-label text-left"><?php echo ': '.strtoupper($data['jenis_transaksi']); ?></label>
		</div>
	</div>
	<?php if ( stristr($data['jenis_pembayaran'], 'supplier') !== FALSE ) { ?>
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="margin-bottom: 5px; padding: 0px 0px 0px 0px;">
				<div class="col-xs-2 no-padding"><label class="control-label text-left">Supplier</label></div>
				<div class="col-xs-10 no-padding">
					<label class="control-label text-left"><?php echo ': '.strtoupper($data['supplier']); ?></label>
				</div>
			</div>
		</div>
	<?php } else if ( stristr($data['jenis_pembayaran'], 'plasma') !== FALSE ) { ?>
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="margin-bottom: 5px; padding: 0px 0px 0px 0px;">
				<div class="col-xs-2 no-padding"><label class="control-label text-left">Peternak</label></div>
				<div class="col-xs-10 no-padding">
					<label class="control-label text-left"><?php echo ': '.strtoupper($data['peternak']); ?></label>
				</div>
			</div>
		</div>
	<?php } else if ( stristr($data['jenis_pembayaran'], 'ekspedisi') !== FALSE ) { ?>
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="margin-bottom: 5px; padding: 0px 0px 0px 0px;">
				<div class="col-xs-2 no-padding"><label class="control-label text-left">Ekspedisi</label></div>
				<div class="col-xs-10 no-padding">
					<label class="control-label text-left"><?php echo ': '.strtoupper($data['ekspedisi']); ?></label>
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="col-xs-12 search left-inner-addon no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-2 no-padding"><label class="control-label text-left">Perusahaan</label></div>
		<div class="col-xs-10 no-padding">
			<label class="control-label text-left"><?php echo ': '.strtoupper($data['perusahaan']); ?></label>
		</div>
	</div>
</form>
<div class="col-xs-12 search left-inner-addon no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
<small>
	<table class="table table-bordered tbl_transaksi" style="margin-bottom: 0px;">
		<thead>
			<tr>
				<th class="col-xs-1">Transaksi</th>
				<th class="col-xs-1">No. Bayar</th>
				<th class="col-xs-2">Tagihan</th>
				<th class="col-xs-2">Bayar</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data['detail'] as $k_det => $v_det): ?>
				<tr>
					<td><?php echo $v_det['transaksi']; ?></td>
					<td><?php echo $v_det['no_bayar']; ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_det['tagihan']); ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_det['bayar']); ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</small>
<div class="col-xs-12 search left-inner-addon no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
<div class="col-xs-12 search left-inner-addon no-padding">
	<label class="control-label">POTONGAN</label>
</div>
<small>
	<table class="table table-bordered tbl_transaksi" style="margin-bottom: 0px;">
		<thead>
			<tr>
				<th class="col-xs-2">No. COA</th>
				<th class="col-xs-6">Nama</th>
				<th class="col-xs-4">Nominal</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( !empty($data['potongan']) && count($data['potongan']) > 0 ): ?>
				<?php foreach ($data['potongan'] as $k_potongan => $v_potongan): ?>
					<tr>
						<td><?php echo $v_potongan['det_jurnal_trans']['sumber_coa']; ?></td>
						<td><?php echo $v_potongan['det_jurnal_trans']['nama']; ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_potongan['nominal']); ?></td>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<tr>
					<td colspan="2">Tidak ada CN</td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>
</small>
<div class="col-xs-12 search left-inner-addon no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
<div class="col-xs-12 search left-inner-addon no-padding">
	<label class="control-label">CREDIT NOTE</label>
</div>
<small>
	<table class="table table-bordered tbl_transaksi" style="margin-bottom: 0px;">
		<thead>
			<tr>
				<th class="col-xs-4">Nama CN</th>
				<th class="col-xs-1">Saldo</th>
				<th class="col-xs-1">Terpakai</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( !empty($data['cn_realisasi_pembayaran']) && count($data['cn_realisasi_pembayaran']) > 0 ): ?>
				<?php foreach ($data['cn_realisasi_pembayaran'] as $k_cn => $v_cn): ?>
					<tr>
						<td><?php echo $v_cn['det_jurnal']['keterangan']; ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_cn['saldo']); ?></td>
						<?php
							$terpakai = $v_cn['saldo'] - $v_cn['sisa_saldo'];
						?>
						<td class="text-right"><?php echo angkaDecimal($terpakai); ?></td>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<tr>
					<td colspan="2">Tidak ada CN</td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>
</small>
<div class="col-xs-12 no-padding" style="margin-top: 5px;">
	<!-- <button type="button" class="btn btn-primary pull-right" onclick="rp.changeTabActive(this)" data-id="<?php echo $data['id']; ?>" data-href="transaksi" data-edit="edit"><i class="fa fa-edit"></i> Edit</button> -->
	<?php if ( $akses['a_delete'] == 1 ): ?>
		<?php if ( $data['delete'] == 1 ): ?>
			<button type="button" class="btn btn-danger pull-right" onclick="rp.delete(this)" data-id="<?php echo $data['id']; ?>" style="margin-right: 0px;"><i class="fa fa-trash"></i> Hapus</button>
		<?php endif ?>
	<?php endif ?>
</div>