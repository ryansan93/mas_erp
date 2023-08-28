<div class="modal-header no-padding" style="padding-bottom: 10px;">
	<span class="modal-title"><b>REKENING MASUK</b></span>
</div>
<div class="modal-body" style="padding-bottom: 0px;">
	<div class="row detailed">
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-12 no-padding"><label class="control-label text-left">Tanggal</label></div>
			<div class="col-xs-12 no-padding">
				<div class="input-group date" id="tgl_rm">
			        <input type="text" class="form-control text-center" data-required="1" placeholder="Tanggal" />
			        <span class="input-group-addon">
			            <span class="glyphicon glyphicon-calendar"></span>
			        </span>
			    </div>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-12 no-padding"><label class="control-label text-left">Perusahaan</label></div>
			<div class="col-xs-12 no-padding">
				<select class="form-control perusahaan" width="100%" data-required="1">
					<option value="">-- Pilih Perusahaan --</option>
					<?php if ( count($perusahaan) > 0 ): ?>
						<?php foreach ($perusahaan as $k_perusahaan => $v_perusahaan): ?>
							<option value="<?php echo $v_perusahaan['kode']; ?>"><?php echo strtoupper($v_perusahaan['nama']); ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-12 no-padding"><label class="control-label text-left">Nominal (Rp.)</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control text-right nominal" data-required="1" placeholder="Nominal" data-tipe="decimal" />
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-12 no-padding"><label class="control-label text-left">Lampiran</label></div>
			<div class="col-lg-12 no-padding">
				<a class="hide" target="_blank"></a>
				<label class="">
					<input type="file" onchange="showNameFile(this)" class="file_lampiran" placeholder="Bukti Rekening Masuk" data-allowtypes="pdf|PDF|jpg|JPG|jpeg|JPEG|png|PNG" style="display: none;">
					<i class="glyphicon glyphicon-paperclip cursor-p"></i>
				</label>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
			<div class="col-xs-12 no-padding"><label class="control-label text-left">Keterangan</label></div>
			<div class="col-xs-12 no-padding">
				<textarea class="form-control keterangan" placeholder="Keterangan"></textarea>
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12 no-padding">
			<div class="col-xs-6 no-padding" style="padding-right: 5px;">
				<button type="button" class="col-xs-12 btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
			</div>
			<div class="col-xs-6 no-padding" style="padding-left: 5px;">
				<button type="button" class="col-xs-12 btn btn-primary" onclick="rt.saveRm()"><i class="fa fa-save"></i> Simpan</button>
			</div>
		</div>
	</div>
</div>