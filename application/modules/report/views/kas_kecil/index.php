<div class="row">
	<div class="col-xs-12">
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding"><label class="control-label">Unit</label></div>
			<div class="col-xs-12 no-padding">
				<select class="form-control unit" data-required="1">
					<option value="all">ALL</option>
					<option value="pusat">PUSAT</option>
					<?php foreach ($unit as $k_unit => $v_unit): ?>
						<option value="<?php echo $v_unit['kode']; ?>"><?php echo $v_unit['nama']; ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding"><label class="control-label">Periode</label></div>
			<div class="col-xs-12 no-padding">
				<div class="input-group date datetimepicker" id="periode">
			        <input type="text" class="form-control text-center" placeholder="PERIODE" data-required="1" />
			        <span class="input-group-addon">
			            <span class="glyphicon glyphicon-calendar"></span>
			        </span>
			    </div>
			</div>
		</div>
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding">
				<button type="button" class="col-xs-12 btn btn-primary" onclick="kk.getLists()"><i class="fa fa-search"></i> Tampilkan</button>
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12 no-padding">
			<small>
				<table class="table table-bordered" style="margin-bottom: 0px;">
					<thead>
						<tr>
							<th class="col-xs-1">Tanggal</th>
							<th class="col-xs-2">Akun Transaksi</th>
							<th class="col-xs-1">PIC</th>
							<th class="col-xs-3">Keterangan</th>
							<th class="col-xs-1">Masuk</th>
							<th class="col-xs-1">Keluar</th>
							<th class="col-xs-1">Saldo</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="7">Data tidak ditemukan.</td>
						</tr>
					</tbody>
				</table>
			</small>
		</div>
		<div class="col-xs-12 no-padding btn-tutup-bulan hide" data-status="0">
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding">
				<button type="button" class="col-xs-12 btn btn-success" onclick="kk.save()"><i class="fa fa-check"></i> Tutup Bulan</button>
			</div>
		</div>
	</div>
</div>