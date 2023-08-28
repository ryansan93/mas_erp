<div class="row content-panel">
	<div class="col-xs-12">
		<div class="col-xs-12 no-padding">
			<div class="col-sm-12 no-padding">
				<label>LABA RUGI PER UNIT</label>
			</div>
			<div class="col-sm-12 no-padding">
				<hr style="margin-top: 10px; margin-bottom: 10px;">
			</div>
			<div class="col-sm-12 no-padding" style="margin-bottom: 10px;">
				<div class="col-sm-12 no-padding">
					<label>UNIT</label>
				</div>
				<div class="col-sm-12 no-padding">
					<select class="form-control unit" data-required="1">
						<option value="all">ALL</option>
						<?php foreach ($unit as $k_unit => $v_unit): ?>
							<option value="<?php echo $v_unit['kode']; ?>"><?php echo $v_unit['nama']; ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<div class="col-sm-12 no-padding" style="margin-bottom: 10px;">
				<div class="col-sm-6 no-padding" style="padding-right: 5px;">
					<div class="col-sm-12 no-padding">
						<label>BULAN</label>
					</div>
					<div class="col-sm-12 no-padding">
						<select class="form-control bulan" data-required="1">
							<option value="all">ALL</option>
							<?php for ($i=1; $i <= 12; $i++) { ?>
								<?php
									$bulan[1] = 'JANUARI';
									$bulan[2] = 'FEBRUARI';
									$bulan[3] = 'MARET';
									$bulan[4] = 'APRIL';
									$bulan[5] = 'MEI';
									$bulan[6] = 'JUNI';
									$bulan[7] = 'JULI';
									$bulan[8] = 'AGUSTUS';
									$bulan[9] = 'SEPTEMBER';
									$bulan[10] = 'OKTOBER';
									$bulan[11] = 'NOVEMBER';
									$bulan[12] = 'DESEMBER';
								?>
								<option value="<?php echo $i; ?>"><?php echo $bulan[ $i ]; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-sm-6 no-padding" style="padding-left: 5px;">
					<div class="col-sm-12 no-padding">
						<label>TAHUN</label>
					</div>
					<div class="col-sm-12 no-padding">
						<div class="input-group date datetimepicker" name="tahun" id="tahun">
					        <input type="text" class="form-control text-center" placeholder="TAHUN" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
				<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="lr.getData()"><i class="fa fa-search"></i> Tampilkan</button>
			</div>
		</div>
	</div>
	<div class="col-xs-12"><hr style="padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 5px;"></div>
	<div class="col-xs-12">
		<small>
			<table class="table table-bordered tbl_laporan" width="100%" cellspacing="0" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="text-left" style="background-color: #ffcd8c;">REKAPITULASI</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Data tidak ditemukan.</td>
					</tr>
				</tbody>
			</table>
		</small>
	</div>
</div>