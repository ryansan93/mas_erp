<div class="row">
	<div class="col-xs-12">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
				<button type="button" class="col-xs-12 btn btn-success pull-left" onclick="dk.changeTab(this)" data-id="" data-edit="" data-href="action"><i class="fa fa-plus"></i> Tambah</button>
			</div>
			<div class="col-xs-12 no-padding"><hr style="padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 5px;"></div>
			<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
				<div class="col-xs-12 no-padding">
					<label class="control-label">Nama Pelanggan</label>
				</div>
				<div class="col-xs-12 no-padding">
					<select class="form-control pelanggan" type="text" data-required="1">
						<option value="">Pilih Pelanggan</option>
						<?php foreach ($pelanggan as $k_plg => $v_plg): ?>
							<option value="<?php echo $v_plg['nomor']; ?>"><?php echo strtoupper($v_plg['nama'].' ('.$v_plg['nama_kab_kota'].')'); ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>

			<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
				<button type="button" class="col-xs-12 btn btn-primary pull-right" onclick="dk.getLists()"><i class="fa fa-search"></i> Tampilkan</button>
			</div>
		</div>
	</div>
	<div class="col-xs-12"><hr style="padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 5px;"></div>
	<div class="col-xs-12">
		<small>
			<span>Klik pada baris untuk melihat detail.</span>
			<table class="table table-bordered tbl_riwayat" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-xs-5">Tanggal</th>
						<th class="col-xs-3">Lokasi</th>
						<th class="col-xs-3">Foto</th>
						<th class="col-xs-1"></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="4">Data tidak ditemukan.</td>
					</tr>
				</tbody>
			</table>
		</small>
	</div>
</div>