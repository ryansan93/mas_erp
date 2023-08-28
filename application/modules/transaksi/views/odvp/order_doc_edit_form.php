<div class="modal-header header">
	<span class="modal-title">Order DOC</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<!-- <h4 class="mb">Add Fitur</h4> -->
		<div class="col-lg-12 detailed">
			<input type="hidden" data-noreg="">

			<form role="form" class="form-horizontal">
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-12 nama_mitra"><b>Nama Mitra + Populasi</b></div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">No Order</div>
					<div class="col-lg-3">
						<input type="text" class="form-control no_order" value="<?php echo $data_order_doc['no_order']; ?>" data-version="<?php echo $data_order_doc['version'];  ?>" readonly>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">Perusahaan</div>
					<div class="col-lg-6">
						<select class="form-control perusahaan" data-required="1">
							<option value="">-- Pilih Perusahaan --</option>
							<?php foreach ($perusahaan as $k_perusahaan => $v_perusahaan): ?>
								<?php
									$selected = null;
									if ( $v_perusahaan['kode'] == $data_order_doc['perusahaan'] ) {
										$selected = 'selected';
									}
								?>
								<option value="<?php echo $v_perusahaan['kode']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($v_perusahaan['perusahaan']); ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">Tgl Order</div>
					<div class="col-lg-4">
						<div class="input-group date col-md-12" id="TglOrder" name="tglOrder" data-tgl="<?php echo substr($data_order_doc['tgl_submit'], 0, 10); ?>">
					        <input type="text" class="form-control text-center" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">Rencana Tiba Kandang</div>
					<div class="col-lg-4">
						<div class="input-group date col-md-12" id="datetimepicker1" name="tgl_tiba_kdg" data-tgl="<?php echo $data_order_doc['rencana_tiba']; ?>">
					        <input type="text" class="form-control text-center" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">Supplier</div>
					<div class="col-lg-5">
						<select class="form-control supplier" data-required="1">
							<option value="">-- Pilih Supplier --</option>
							<?php foreach ($supplier as $k_supl => $v_supl): ?>
								<?php
									$selected = null;
									if ( $v_supl['nomor'] == $data_order_doc['supplier'] ) {
										$selected = 'selected';
									}
								?>
								<option value="<?php echo $v_supl['nomor']; ?>" <?php echo $selected; ?> ><?php echo $v_supl['nama']; ?></option>								
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">DOC</div>
					<div class="col-lg-4">
						<!-- <input type="text" class="form-control jns_doc" value="DOC grade A Platinum" placeholder="Jenis Box" data-required="1"> -->
						<select class="form-control jns_doc" data-required="1">
							<option value="">-- Pilih Jenis DOC --</option>
							<?php foreach ($data_doc as $k_doc => $v_doc): ?>
								<?php
									$selected = null;
									if ( $v_doc['kode'] == $data_order_doc['item'] ) {
										$selected = 'selected';
									}
								?>
								<option value="<?php echo $v_doc['kode']; ?>" <?php echo $selected; ?> ><?php echo $v_doc['nama']; ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">Jenis Box</div>
					<div class="col-lg-3">
						<input type="text" class="form-control jns_box" value="PLASTIK" placeholder="Jenis Box" data-required="1">
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">Jumlah</div>
					<div class="col-lg-2">
						<input type="text" class="form-control text-right ekor" placeholder="Ekor" data-tipe="integer" maxlength="7" onblur="odvp.hit_box(this)" data-required="1" value="<?php echo angkaRibuan($data_order_doc['jml_ekor']); ?>">
					</div>
					<div class="col-sm-1">Ekor</div>
					<div class="col-lg-2">
						<input type="text" class="form-control text-right box" placeholder="Box" data-tipe="integer" maxlength="5" readonly data-required="1" value="<?php echo angkaRibuan($data_order_doc['jml_box']); ?>">
					</div>
					<div class="col-sm-1">Box</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">Harga</div>
					<div class="col-lg-2">
						<input type="text" class="form-control text-right harga" placeholder="Harga" data-tipe="integer" maxlength="6" onblur="odvp.hit_total_order_doc(this)" value="<?php echo angkaRibuan($data_order_doc['harga']); ?>" data-required="1">
					</div>
					<div class="col-lg-3">
						<input type="text" class="form-control text-right total" placeholder="Total" data-tipe="integer" maxlength="10" readonly value="<?php echo angkaRibuan($data_order_doc['total']); ?>" data-required="1">
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-4">Keterangan</div>
					<div class="col-lg-8">
						<!-- <input type="text" class="form-control ket" value="" placeholder="Keterangan" data-required="1"> -->
						<textarea class="form-control ket"><?php echo angkaRibuan($data_order_doc['keterangan']); ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-12 no-padding">
						<hr style="margin-top: 5px; margin-bottom: 5px;">
						<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-left" title="ADD" onclick="odvp.edit_order_doc(this)" style="margin-left: 10px;"> 
							<i class="fa fa-edit" aria-hidden="true"></i> Edit
						</button>
						<!-- <button id="btn-add" type="button" data-href="action" class="btn btn-danger cursor-p pull-left" title="ADD" onclick="rdim.changeTabActive(this)" style="margin-left: 10px;"> 
							<i class="fa fa-times" aria-hidden="true"></i> Batal
						</button> -->
					</div>
				</div>
			</form>
		</div>
	</div>
</div>