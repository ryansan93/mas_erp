<div class="modal-header no-padding">
	<span class="modal-title"><b>PILIH CN</b></span>
</div>
<div class="modal-body" style="padding-bottom: 0px;">
	<div class="row detailed">
		<div class="col-xs-12 detailed no-padding">
			<table class="table table-bordered" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-xs-7 text-center">Nama CN</th>
						<th class="col-xs-4 text-center">Saldo</th>
						<th class="col-xs-1 text-center">Pilih</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<tr>
								<td><?php echo $v_data['keterangan']; ?></td>
								<td class="text-right saldo"><?php echo angkaDecimal($v_data['saldo']); ?></td>
								<td class="text-center">
									<input type="checkbox" class="cursor-p check" data-id="<?php echo $v_data['id']; ?>">
								</td>
							</tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="3">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
		<div class="col-xs-12 detailed no-padding">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-xs-12 detailed no-padding">
			<div class="col-xs-6 no-padding" style="padding-right: 5px;">
				<button type="button" class="col-xs-12 btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
			</div>
			<div class="col-xs-6 no-padding" style="padding-left: 5px;">
				<button type="button" class="col-xs-12 btn btn-primary" onclick="rp.pilihCN(this)"><i class="fa fa-check"></i> Pilih</button>
			</div>
		</div>
	</div>
</div>