<div class="modal-header header">
	<span class="modal-title">Terima DOC Dikandang</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row detailed">
		<div class="col-lg-12 detailed">
			<form role="form" class="form-horizontal">
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-2">No Order</div>
					<div class="col-lg-3">
						<input type="text" class="form-control no_order" value="<?php echo $data_order_doc['no_order']; ?>" readonly>
					</div>
					<div class="col-lg-2">No SJ</div>
					<div class="col-lg-3">
						<input type="text" class="form-control no_sj" data-required="1">
					</div>
					<div class="col-lg-2" style="padding-top: 2px;">
						<a name="dokumen" class="text-right hide sj" target="_blank" style="padding-right: 10px;"><i class="fa fa-file"></i></a>
						<label class="" style="margin-bottom: 0px;">
                        	<input style="display: none;" placeholder="Dokumen" class="file_lampiran_sj no-check" type="file" onchange="odvp.showNameFile(this)" data-name="no-name" data-allowtypes="doc|pdf|docx|jpg|jpeg|png" data-required="1">
                        	<i class="glyphicon glyphicon-paperclip cursor-p" title="Attachment SJ"></i> 
                      	</label>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-3">Tgl Kirim DOC</div>
					<div class="col-lg-4">
						<div class="input-group date col-md-12" id="datetimepicker2" name="tgl_kirim_doc">
					        <input type="text" class="form-control text-center" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-3">Tgl Tiba Kandang</div>
					<div class="col-lg-4">
						<div class="input-group date col-md-12" id="datetimepicker1" name="tgl_tiba_kdg">
					        <input type="text" class="form-control text-center" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-3">No Polisi</div>
					<div class="col-lg-3">
						<input type="text" class="form-control nopol" data-required="1">
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-3">Supplier</div>
					<div class="col-lg-5">
						<select class="form-control supplier" data-required="1" disabled>
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
					<div class="col-lg-3">DOC</div>
					<div class="col-lg-4">
						<select class="form-control jns_doc" data-required="1" disabled>
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
					<div class="col-lg-3">Jenis Box</div>
					<div class="col-lg-3">
						<input type="text" class="form-control jns_box" value="PLASTIK" placeholder="Jenis Box" data-required="1" readonly>
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-3">Jumlah</div>
					<div class="col-lg-2">
						<input type="text" class="form-control text-right ekor" data-tipe="integer" maxlength="7" onblur="odvp.hit_box(this)" value="<?php echo angkaRibuan($data_order_doc['jml_ekor']); ?>" data-required="1" placeholder="EKOR">
					</div>
					<div class="col-sm-1">Ekor</div>
					<div class="col-lg-2">
						<input type="text" class="form-control text-right box" data-tipe="integer" maxlength="5" readonly value="<?php echo angkaRibuan($data_order_doc['jml_box']); ?>" data-required="1" placeholder="BOX">
					</div>
					<div class="col-sm-1">Box</div>
					<div class="col-lg-2">
						<input type="text" class="form-control text-right bb" data-tipe="decimal3" maxlength="7" data-required="1" placeholder="BB">
					</div>
					<div class="col-sm-1">Kg</div>
				</div>
				<div class="form-group align-items-center hide">
					<div class="col-lg-3">Harga</div>
					<div class="col-lg-2">
						<input type="text" class="form-control text-right harga" data-tipe="integer" maxlength="7" onblur="odvp.hit_total_order_doc(this)" value="<?php echo angkaRibuan($data_order_doc['harga']); ?>" data-required="1" placeholder="HARGA">
					</div>
					<div class="col-lg-3">
						<input type="text" class="form-control text-right total" data-tipe="integer" maxlength="5" readonly value="<?php echo angkaRibuan($data_order_doc['total']); ?>" data-required="1" placeholder="TOTAL">
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-3">Kondisi</div>
					<div class="col-lg-3">
						<input type="text" class="form-control kondisi" data-required="1">
					</div>
				</div>
				<div class="form-group d-flex align-items-center">
					<div class="col-lg-3">Keterangan</div>
					<div class="col-lg-8">
						<textarea class="form-control ket"></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-12 no-padding">
						<hr style="margin-top: 5px; margin-bottom: 5px;">
						<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-left" title="ADD" onclick="odvp.save_terima_doc(this)" style="margin-left: 10px;"> 
							<i class="fa fa-save" aria-hidden="true"></i> Simpan
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>