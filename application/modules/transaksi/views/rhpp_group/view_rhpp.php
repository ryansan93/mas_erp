<div class="panel-body" style="margin-top: 0px; padding-top: 0px;">
    <div class="row new-line">
        <div class="col-sm-12 view_form no-padding">
        	<?php
				$hide_plasma = null;
				$hide_inti = null;
				$active_plasma = null;
				$active_plasma_div = null;
				$active_inti = null;
				$active_inti_div = null;
				$style_hide_inti = null;
				if ( $data['jenis_mitra'] == 'ME' ) {
					if ( $form_rhpp_inti == 0 ) {
						$style_hide_inti = 'display: none;';
					}
					$hide_inti = 'hide';
					$active_plasma = 'active';
					$active_plasma_div = 'show active';
				} else {
					$hide_plasma = 'hide';
					$active_inti = 'active';
					$active_inti_div = 'show active';
				}
			?>
        	<form class="form-vertical" role="form">
		        <!-- <div class="form-group d-flex align-items-center"> -->
		        	<?php if ( empty($id) ): ?>
		        		<div class="col-sm-12 no-padding" style="margin-bottom: 10px;">
			        		<div class="col-sm-6 no-padding">
			        			<div class="col-sm-12 no-padding align-items-center" style="margin-bottom: 10px;">
			        				<div class="col-md-3 no-padding">
				                		<label class="control-label" style="padding-top: 7px;">Tutup Siklus</label>
				                	</div>
				                	<div class="col-md-4">
				                		<div class="input-group date datetimepicker" name="tglTutup" id="TglTutup">
									        <input type="text" class="form-control text-center" placeholder="Tutup Siklus" data-required="1" data-val="<?php echo $tanggal; ?>" />
									        <span class="input-group-addon">
									            <span class="glyphicon glyphicon-calendar"></span>
									        </span>
									    </div>
				                	</div>
			        			</div>
			        			<div class="col-sm-12 no-padding align-items-center <?php echo $hide_plasma; ?>" style="margin-bottom: 10px;">
				                	<div class="col-sm-12 no-padding">
					                	<div class="col-md-3 no-padding">
					                		<label class="control-label" style="padding-top: 7px;">Biaya Materai</label>
					                	</div>
					                	<div class="col-md-4">
					                		<?php
					                			$biaya_materai = null;
					                			if ( $data['jenis_mitra'] != 'ME' ) {
					                				$biaya_materai = angkaRibuan(0);
					                			}
					                		?>
					                		<input type="text" class="form-control text-right biaya_materai" data-tipe="integer" data-required="1" onblur="rg.hit_tot_pengeluaran(this)" maxlength="7" value="<?php echo $data['biaya_materai']; ?>">
					                	</div>
				                	</div>
					            </div>
					            <div class="col-sm-12 no-padding align-items-center <?php echo $hide_plasma; ?>">
				                	<div class="col-sm-12 no-padding">
					                	<div class="col-md-3 no-padding">
					                		<label class="control-label" style="padding-top: 7px;">Potongan Pajak</label>
					                	</div>
					                	<div class="col-md-4">
					                		<select class="form-control prs_potongan" data-required="1" onchange="rg.hit_potongan_pajak(this)">
				    							<option value="">Pilih</option>
				    							<?php foreach ($data['data_potongan_pajak'] as $k_dpp => $v_dpp): ?>
				    								<?php
			        									$selected = null;
			        									if ( $data['jenis_mitra'] != 'ME' ) {
				        									if ( $v_dpp['prs_potongan'] == 0 ) {
				        										$selected = 'selected';
				        									}
			        									}
			        								?>
				    								<option value="<?php echo $v_dpp['id']; ?>" <?php echo $selected; ?> ><?php echo angkaDecimal($v_dpp['prs_potongan']); ?></option>
				    							<?php endforeach ?>
				    						</select>
					                	</div>
					                	<div class="col-md-1 no-padding">
					                		<label class="control-label" style="padding-top: 7px;">%</label>
					                	</div>
				                	</div>
				                </div>
			        		</div>
			        		<div class="col-sm-6 no-padding">
				            	<button type="button" class="btn btn-primary pull-right" onclick="rg.save(this)"><i class="fa fa-save"></i> Simpan</button>
			        		</div>
		        		</div>
			        <?php else: ?>
			        	<div class="col-md-12 no-padding" style="margin-bottom: 10px;">
			        		<div class="col-sm-6 no-padding">
		            			<div class="col-sm-12 no-padding <?php echo $hide_inti; ?>">
		            				<?php if ( $data['cn'] == '' ): ?>
					            		<div class="col-sm-12 d-flex align-items-center">
					            			<div class="col-sm-1 d-flex align-items-center no-padding"><label class="control-label" style="padding-top: 0px;">CN : </label></div>
					            			<div class="col-sm-4">
					            				<input type="text" class="form-control text-right nilai_cn" data-tipe="decimal" placeholder="Nilai CN" onblur="rg.setCn(this)" data-required="1">
					            			</div>
					            			<div class="col-sm-2">
					            				<button type="button" class="btn btn-primary" onclick="rg.submitCn(this)" data-id="<?php echo $id; ?>">Submit CN</button>
					            			</div>
					            		</div>	            					
		            				<?php endif ?>
		            			</div>
		            		</div>
				        	<div class="col-sm-6 no-padding" style="margin-bottom: 10px;">
			            		<button type="button" class="btn btn-danger pull-right" onclick="rg.delete(this)" data-id="<?php echo $id; ?>"><i class="fa fa-times"></i> Delete</button>
			            	</div>
			        	</div>
		        	<?php endif ?>
		        <!-- </div> -->
		    </form>
            <fieldset>
	        	<div class="panel-heading no-padding">
					<ul class="nav nav-tabs nav-justified">
						<li class="nav-item <?php echo $hide_plasma; ?>">
							<a class="nav-link <?php echo $active_plasma; ?>" data-toggle="tab" href="#rhpp_plasma" data-tab="rhpp_plasma">RHPP PLASMA</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo $active_inti; ?>" data-toggle="tab" href="#rhpp_inti" data-tab="rhpp_inti" style="<?php echo $style_hide_inti; ?>">RHPP INTI</a>
						</li>
					</ul>
				</div>
				<div class="panel-body" style="padding-bottom: 0px;">
					<div class="tab-content">
						<div id="rhpp_plasma" class="tab-pane fade <?php echo $active_plasma_div; ?> <?php echo $hide_plasma; ?>">
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
				                	<div class="col-md-1 no-padding">
				                		<label class="control-label">Mitra</label>
				                	</div>
				                	<div class="col-md-5 no-padding">
				                        <label class="control-label mitra" data-val="<?php echo $data['mitra']; ?>" data-nomor="<?php echo $data['nomor']; ?>">: <?php echo $data['mitra']; ?></label>
				                	</div>
				                	<div class="col-md-1 no-padding">&nbsp;</div>
				                	<div class="col-md-2 no-padding">
				                		<label class="control-label">Total Populasi</label>
				                	</div>
				                	<div class="col-md-3 no-padding">
				                        <label class="control-label tot_populasi" data-val="<?php echo $data['tot_populasi']; ?>">: <?php echo angkaRibuan($data['tot_populasi']); ?></label>
				                	</div>
				                </div>
				                <div class="form-group"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				                <div class="form-group">
				                	<table class="table table-nobordered tbl_list_noreg" style="margin-bottom: 0px;">
				                		<thead>
				                			<tr>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>No. Reg</b>
				                						</div>
				                					</div>
				                				</td>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>Kandang</b>
				                						</div>
				                					</div>
				                				</td>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>Populasi</b>
				                						</div>
				                					</div>
				                				</td>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>Tgl Chick In</b>
				                						</div>
				                					</div>
				                				</td>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>Tgl Tutup Siklus</b>
				                						</div>
				                					</div>
				                				</td>
				                			</tr>
				                		</thead>
				                		<tbody>
				                			<?php foreach ($data['detail'] as $k_detail => $v_detail): ?>
					                			<tr>
					                				<td class="noreg" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['noreg']; ?>"><?php echo $v_detail['noreg']; ?></td>
					                				<td class="kandang" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['kandang']; ?>"><?php echo (int) $v_detail['kandang']; ?></td>
					                				<td class="populasi" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['populasi']; ?>"><?php echo angkaRibuan($v_detail['populasi']); ?></td>
					                				<td class="tgl_docin" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['tgl_docin']; ?>"><?php echo tglIndonesia($v_detail['tgl_docin'], '-', ' '); ?></td>
					                				<td class="tgl_tutup" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['tgl_tutup']; ?>"><?php echo tglIndonesia($v_detail['tgl_tutup'], '-', ' '); ?></td>
					                			</tr>
				                			<?php endforeach ?>
				                		</tbody>
				                	</table>
				                </div>
				            </form>
				            <div class="form-group"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				            <?php
				            	$populasi = $data['tot_populasi'];
				            	$rata_umur_panen = $data['rata_umur_panen'];

				            	$total_nilai_doc = 0;
				            	$total_nilai_pakan = 0;
				            	$total_nilai_pemakaian = 0;

				            	$total_jumlah_pakan = 0;
				            ?>
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
				                	<small>
					                	<table class="table table-bordered pemakaian">
					                		<thead>
					                			<tr>
					                				<th class="col-sm-1 text-center">Tanggal</th>
					                				<th class="col-sm-1 text-center">Nota / SJ</th>
					                				<th class="col-sm-2 text-center">Barang</th>
					                				<th class="col-sm-1 text-center">Box / Sak</th>
					                				<th class="col-sm-1 text-center">Jumlah</th>
					                				<th class="col-sm-1 text-center">Harga</th>
					                				<th class="col-sm-2 text-center">Total</th>
					                			</tr>
					                		</thead>
					                		<tbody>
					                			<?php $total_pemakaian = 0; ?>
					                			<tr class="head">
					                				<td colspan=7"><b>DOC</b></td>
					                			</tr>
					                			<?php 
					                				$total_box = 0;
				                					$total_jumlah = 0;
				                					$total_nilai = 0;
					                			?>
					                			<?php if ( !empty($data_plasma['detail']['data_doc']) ): ?>
					                				<?php 
					                					$data_doc = $data_plasma['detail']['data_doc']['doc'];
					                					$data_vaksin = $data_plasma['detail']['data_doc']['vaksin'];
					                				?>
					                				<?php if ( !empty($data_doc) ): ?>
					                					<?php foreach ($data_doc as $k_doc => $v_doc): ?>
								                			<tr class="data_doc" data-key="<?php echo $k_doc; ?>">
								                				<td class="text-left tanggal" data-val="<?php echo $v_doc['tgl_docin']; ?>"><?php echo tglIndonesia($v_doc['tgl_docin'], '-', ' '); ?></td>
								                				<td class="text-center nota" data-val="<?php echo $v_doc['sj']; ?>"><?php echo $v_doc['sj']; ?></td>
								                				<td class="barang" data-val="<?php echo $v_doc['barang']; ?>"><?php echo $v_doc['barang']; ?></td>
								                				<td class="text-right box_zak" data-val="<?php echo $v_doc['box']; ?>"><?php echo angkaRibuan($v_doc['box']); ?></td>
								                				<td class="text-right jumlah" data-val="<?php echo $v_doc['jumlah']; ?>"><?php echo angkaRibuan($v_doc['jumlah']); ?></td>
								                				<td class="text-right harga" data-val="<?php echo $v_doc['harga']; ?>"><?php echo angkaRibuan($v_doc['harga']); ?></td>
								                				<td class="text-right total" data-val="<?php echo $v_doc['total']; ?>"><?php echo angkaRibuan($v_doc['total']); ?></td>
								                			</tr>
								                			<?php
								                				$total_box += $v_doc['box'];
							                					$total_jumlah += $v_doc['jumlah'];
							                					$total_nilai += $v_doc['total'];
								                			?>
					                					<?php endforeach ?>
					                				<?php endif ?>
					                				<?php if ( !empty($data_vaksin) ): ?>
					                					<?php foreach ($data_vaksin as $k_vaksin => $v_vaksin): ?>
								                			<tr class="data_vaksin" data-key="<?php echo $k_vaksin; ?>">
								                				<td colspan="2"></td>
								                				<td class="vaksin" data-val="<?php echo $v_vaksin['barang']; ?>"><?php echo $v_vaksin['barang']; ?></td>
								                				<td colspan="2"></td>
								                				<td class="text-right harga_vaksin" data-val="<?php echo $v_vaksin['harga']; ?>"><?php echo angkaRibuan($v_vaksin['harga']); ?></td>
								                				<td class="text-right total_vaksin" data-val="<?php echo $v_vaksin['total']; ?>"><?php echo angkaRibuan($v_vaksin['total']); ?></td>
								                			</tr>
								                			<?php
							                					$total_nilai += $v_vaksin['total'];
								                			?>
					                					<?php endforeach ?>
					                				<?php endif ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_box); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php $total_nilai_doc = $total_nilai; ?>
					                			<?php endif ?>

					                			<?php $total_pemakaian_zak = 0; ?>
					                			<?php $total_pemakaian_jumlah = 0; ?>
					                			<?php $total_pemakaian_nilai = 0; ?>
					                			<tr class="head">
					                				<td colspan="7"><b>PAKAN</b></td>
					                			</tr>
					                			<?php
					                				$total_zak = 0;
				                					$total_jumlah = 0;
				                					$total_nilai = 0;
					                			?>
					                			<?php if ( !empty($data_plasma['detail']['data_pakan']) ): ?>
					                				<?php 
					                					$data_pakan = $data_plasma['detail']['data_pakan']; 
					                				?>
					                				<?php foreach ($data_pakan as $k => $val): ?>
					                					<tr class="data_pakan">
							                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo empty($val['sj']) ? null : $val['sj']; ?>"><?php echo empty($val['sj']) ? '-' : $val['sj']; ?></td>
							                				<td class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<td class="text-right box_zak" data-val="<?php echo $val['zak']; ?>"><?php echo angkaRibuan($val['zak']); ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaRibuan($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaRibuan($val['harga']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php
							                				$total_zak += $val['zak'];
						                					$total_jumlah += $val['jumlah'];
						                					$total_nilai += $val['total'];
							                			?>
							                			<?php $total_pemakaian_zak += $val['zak']; ?>
							                			<?php $total_pemakaian_jumlah += $val['jumlah']; ?>
							                			<?php $total_pemakaian_nilai += $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL PENGIRIMAN</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php 
					                					$total_nilai_pakan = $total_nilai;
					                					$total_jumlah_pakan = $total_jumlah;
					                				?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>PINDAH PAKAN</b></td>
					                			</tr>
					                			<?php if ( !empty($data_plasma['detail']['data_pindah_pakan']) ): ?>
					                				<?php 
					                					$data_pindah_pakan = $data_plasma['detail']['data_pindah_pakan']; 
					                					$total_zak = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_pindah_pakan as $k => $val): ?>
					                					<tr class="data_pindah_pakan">
							                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo empty($val['sj']) ? null : $val['sj']; ?>"><?php echo empty($val['sj']) ? '-' : $val['sj']; ?></td>
							                				<td class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<td class="text-right box_zak" data-val="<?php echo $val['zak']; ?>"><?php echo angkaRibuan($val['zak']); ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaRibuan($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaRibuan($val['harga']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php
							                				$total_zak += $val['zak'];
						                					$total_jumlah += $val['jumlah'];
						                					$total_nilai += $val['total'];
							                			?>
							                			<?php $total_pemakaian_zak -= $val['zak']; ?>
							                			<?php $total_pemakaian_jumlah -= $val['jumlah']; ?>
							                			<?php $total_pemakaian_nilai -= $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL PENGIRIMAN</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php 
					                					$total_nilai_pakan -= $total_nilai; 
					                					$total_jumlah_pakan -= $total_jumlah;
					                				?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>RETUR PAKAN</b></td>
					                			</tr>
					                			<?php if ( !empty($data_plasma['detail']['data_retur_pakan']) ): ?>
					                				<?php 
					                					$data_retur_pakan = $data_plasma['detail']['data_retur_pakan'];
					                					$total_zak = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_retur_pakan as $k => $val): ?>
					                					<tr class="data_retur_pakan">
							                				<td class="text-left" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo $val['no_retur']; ?>"><?php echo $val['no_retur']; ?></td>
							                				<td class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<?php $zak = ($val['jumlah'] > 0) ? $val['jumlah']/50 : 0; ?>
							                				<td class="text-right box_zak" data-val="<?php echo $zak; ?>"><?php echo angkaRibuan($zak); ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaDecimal($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaRibuan($val['harga']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php $total_zak += $zak; ?>
							                			<?php $total_nilai += $val['total']; ?>
							                			<?php $total_jumlah += $val['jumlah']; ?>

							                			<?php $total_pemakaian_zak -= $zak; ?>
														<?php $total_pemakaian_jumlah -= $val['jumlah']; ?>
														<?php $total_pemakaian_nilai -= $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL RETUR</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php $total_nilai_pakan -= $total_nilai; ?>
					                				<?php $total_jumlah_pakan -= $total_jumlah; ?>

					                			<?php endif ?>
					                			<tr>
				                					<td class="text-right" colspan="3"><b>TOTAL PEMAKAIAN</b></td>
				                					<td class="text-right"><b><?php echo angkaRibuan($total_pemakaian_zak); ?></b></td>
				                					<td class="text-right"><b><?php echo angkaDecimal($total_pemakaian_jumlah) ?></b></td>
				                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_pemakaian_nilai); ?></b></td>
				                				</tr>

					                			<tr class="head">
					                				<td colspan="7"><b>OVK</b></td>
					                			</tr>
					                			<?php if ( !empty($data_plasma['detail']['data_voadip']) ): ?>
					                				<?php 
					                					$data_voadip = $data_plasma['detail']['data_voadip'];
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_voadip as $k => $val): ?>
					                					<tr class="data_voadip">
							                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo $val['sj']; ?>"><?php echo $val['sj']; ?></td>
							                				<td colspan="2" class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaDecimal($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaDecimalFormat($val['harga'], $val['decimal']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php $total_nilai += $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="4"><b>TOTAL PENGIRIMAN</b></td>
					                					<td class="text-right" colspan="3"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php $total_pemakaian += $total_nilai; ?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>RETUR OVK</b></td>
					                			</tr>
					                			<?php if ( !empty($data_plasma['detail']['data_retur_voadip']) ): ?>
					                				<?php 
					                					$data_retur_voadip = $data_plasma['detail']['data_retur_voadip'];
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_retur_voadip as $k => $val): ?>
					                					<tr class="data_retur_voadip">
							                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo $val['no_retur']; ?>"><?php echo $val['no_retur']; ?></td>
							                				<td colspan="2" class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaDecimal($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaDecimalFormat($val['harga'], $val['decimal']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php $total_nilai += $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="4"><b>TOTAL RETUR</b></td>
					                					<td class="text-right" colspan="3"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php $total_pemakaian -= $total_nilai; ?>
					                			<?php endif ?>

					                			<tr>
				                					<td class="text-right" colspan="4"><b>TOTAL PEMAKAIAN</b></td>
				                					<td class="text-right" colspan="3"><b><?php echo angkaRibuan($total_pemakaian); ?></b></td>
				                				</tr>
				                				<?php $total_nilai_pemakaian = $total_pemakaian; ?>
					                		</tbody>
					                	</table>
					                </small>
				                </div>
				            </form>
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
				                	<hr style="margin-top: 0px;">
				                </div>
				            </form>
				            <?php
								$total_ekor = 0;
								$total_tonase = 0;
								$total_nilai_kontrak = 0;
								$total_nilai_pasar = 0;
								$total_nilai_insentif = 0;

								$total_pembelian_sapronak = $total_nilai_doc + $total_nilai_pakan + $total_nilai_pemakaian;
								$bonus_kematian = 0;
								$bonus_insentif_fcr = 0;
				            ?>
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
									<fieldset>
										<legend>Penjualan Ayam Peternak</legend>
										<small>
											<table class="table table-bordered penjualan_ayam" style="margin-bottom: 0px;">
												<thead>
													<tr>
														<th class="col-sm-1 text-center">Tanggal</th>
														<th class="col-sm-1 text-center">DO</th>
														<th class="col-sm-2 text-center">Pembeli</th>
														<th class="col-sm-1 text-center">Ekor</th>
														<th class="col-sm-1 text-center">Tonase (Kg)</th>
														<th class="text-center">BB Rata2</th>
														<th class="text-center">Kontrak</th>
														<th class="col-sm-1 text-center">Total</th>
														<th class="text-center">Hrg Pasar</th>
														<th class="col-sm-1 text-center">Total</th>
														<th class="text-center">Selisih</th>
														<th class="text-center">Insentif</th>
														<th class="col-sm-1 text-center">Total</th>
													</tr>
												</thead>
												<tbody>
													<?php if ( !empty($data_plasma['detail']['data_rpah']) ): ?>
														<?php $data_rpah = $data_plasma['detail']['data_rpah']; ?>
														<?php foreach ($data_rpah as $k => $val): ?>
															<tr class="data_penjualan">
																<td class="tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
																<td class="text-center nota" data-val="<?php echo $val['do'] ?>"><?php echo $val['do'] ?></td>
																<td class="pembeli" data-val="<?php echo $val['pembeli'] ?>"><?php echo $val['pembeli'] ?></td>
																<td class="text-right ekor" data-val="<?php echo $val['ekor']; ?>"><?php echo angkaRibuan($val['ekor']); ?></td>
																<td class="text-right tonase" data-val="<?php echo $val['tonase']; ?>"><?php echo angkaDecimal($val['tonase']); ?></td>
																<td class="text-right bb" data-val="<?php echo $val['bb']; ?>"><?php echo angkaDecimal($val['bb']); ?></td>
																<td class="text-right harga_kontrak" data-val="<?php echo $val['hrg_kontrak']; ?>"><?php echo angkaRibuan($val['hrg_kontrak']); ?></td>
																<td class="text-right total_kontrak" data-val="<?php echo $val['total_kontrak']; ?>"><?php echo angkaRibuan($val['total_kontrak']); ?></td>
																<td class="text-right harga_pasar" data-val="<?php echo $val['hrg_pasar']; ?>"><?php echo angkaRibuan($val['hrg_pasar']); ?></td>
																<td class="text-right total_pasar" data-val="<?php echo $val['total_pasar']; ?>"><?php echo angkaRibuan($val['total_pasar']); ?></td>
																<td class="text-right selisih" data-val="<?php echo $val['selisih']; ?>"><?php echo angkaRibuan($val['selisih']); ?></td>
																<td class="text-right insentif" data-val="<?php echo $val['insentif']; ?>"><?php echo angkaRibuan($val['insentif']); ?></td>
																<td class="text-right total_insentif" data-val="<?php echo $val['total_insentif']; ?>"><?php echo angkaRibuan($val['total_insentif']); ?></td>
															</tr>
															<?php
																$total_ekor += $val['ekor'];
																$total_tonase += $val['tonase'];
																$total_nilai_kontrak += $val['total_kontrak'];
																$total_nilai_pasar += $val['total_pasar'];
																$total_nilai_insentif += $val['total_insentif'];
															?>
														<?php endforeach ?>
														<tr>
															<td class="text-right" colspan="3"><b>TOTAL</b></td>
															<td class="text-right"><b><?php echo angkaRibuan($total_ekor); ?></b></td>
															<td class="text-right"><b><?php echo angkaDecimal($total_tonase); ?></b></td>
															<td colspan="2"></td>
															<td class="text-right"><b><?php echo angkaRibuan($total_nilai_kontrak); ?></b></td>
															<td></td>
															<td class="text-right"><b><?php echo angkaRibuan($total_nilai_pasar); ?></b></td>
															<td colspan="2"></td>
															<td class="text-right"><b><?php echo angkaRibuan($total_nilai_insentif); ?></b></td>
														</tr>
														<?php 
															// $fcr = 0;
															// if ( $total_jumlah_pakan > 0 && $total_tonase > 0 ) {
															// 	$fcr = $total_jumlah_pakan / $total_tonase;
															// } 
															$fcr = $data['fcr'];
														?>
											            <?php 
															// $bb = 0;
															// if ( $total_ekor > 0 && $total_tonase > 0 ) {
															// 	$bb = $total_tonase / $total_ekor;
															// } 
											            	$bb = $data['bb'];
														?>
														<?php
															// $deplesi = 0;
															// if ( $populasi > 0 && $total_ekor > 0 ) {
															// 	$deplesi = abs((($populasi - $total_ekor) / $populasi) * 100);
															// }
															$deplesi = $data['deplesi'];
														?>
														<?php
															// $ip = 0;
															// if ( $deplesi > 0 && $bb > 0 && $fcr > 0 && $rata_umur_panen > 0 ) {
															// 	$ip = ((100 - $deplesi) * $bb) / ($fcr * $rata_umur_panen) * 100;
															// }
															$ip = $data['ip'];
														?>
														<?php
															$bonus_kematian = ($deplesi <= 5) ? 25 * $total_tonase : 0;
															$bonus_insentif_fcr = $data['bonus_insentif_fcr'];
														?>
													<?php else: ?>
														<tr class="text-center" colspan="13">Data tidak ditemukan.</tr>
													<?php endif ?>
												</tbody>
											</table>
										</small>
									</fieldset>
								</div>
				            </form>
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
				                	<hr>
				                </div>
				            </form>
				            <?php
								$total_bonus_insentif_listrik = $data['total_bonus_insentif_listrik'];

				            	$total_pemasukan = $total_nilai_kontrak + $total_nilai_insentif + $bonus_kematian + $bonus_insentif_fcr + $total_bonus_insentif_listrik;
				            	$total_pengeluaran = $total_pembelian_sapronak + $data['biaya_materai'];
				            ?>
				            <form class="form-horizontal" role="form">
				            	<div class="form-group">
					                <div class="col-sm-6" style="padding-left: 0px;">
										<fieldset>
											<legend>Rekapitulasi Peternak</legend>
											<small>
												<table class="table table-nobordered" style="margin-bottom: 0px;">
													<tbody>
														<tr class="top">
															<td class="col-sm-6 kiri kanan">Penjualan Ayam</td>
															<td class="col-sm-3 text-right kanan tot_penjualan_ayam" data-val="<?php echo $total_nilai_kontrak; ?>"><?php echo angkaRibuan($total_nilai_kontrak); ?></td>
															<td class="col-sm-3 text-right kanan">-</td>
														</tr>
														<tr>
															<td class="col-sm-6 kiri kanan">Total Pembelian Sapronak</td>
															<td class="col-sm-3 text-right kanan">-</td>
															<td class="col-sm-3 text-right kanan tot_pembelian_sapronak" data-val="<?php echo $total_pembelian_sapronak; ?>"><?php echo angkaRibuan($total_pembelian_sapronak); ?></td>
														</tr>
														<tr>
															<td class="col-sm-6 kiri kanan">Biaya Materai</td>
															<td class="col-sm-3 text-right kanan">-</td>
															<td class="col-sm-3 text-right kanan">
																<input type="hidden" class="total_pembelian_sapronak" value="<?php echo $total_pembelian_sapronak; ?>">
																<span class="biaya_materai" data-val="<?php echo $data['biaya_materai']; ?>"><?php echo angkaRibuan($data['biaya_materai']); ?></span>
															</td>
														</tr>
														<tr>
															<td class="col-sm-6 kiri kanan persen_bonus_pasar" data-val="<?php echo $data['bonus_pasar']; ?>">Bonus Pasar <?php echo $data['bonus_pasar']; ?>%</td>
															<td class="col-sm-3 text-right kanan bonus_pasar" data-val="<?php echo $total_nilai_insentif; ?>"><?php echo angkaRibuan($total_nilai_insentif); ?></td>
															<td class="col-sm-3 text-right kanan">-</td>
														</tr>
														<tr>
															<td class="col-sm-6 kiri kanan">Bonus Kematian</td>
															<td class="col-sm-3 text-right kanan bonus_kematian" data-val="<?php echo $bonus_kematian; ?>"><?php echo angkaRibuan($bonus_kematian); ?></td>
															<td class="col-sm-3 text-right kanan">-</td>
														</tr>
														<tr>
															<td class="col-sm-6 kiri kanan">Bonus Insentif FCR</td>
															<td class="col-sm-3 text-right kanan bonus_insentif_fcr" data-val="<?php echo $bonus_insentif_fcr; ?>"><?php echo angkaRibuan($bonus_insentif_fcr); ?></td>
															<td class="col-sm-3 text-right kanan">-</td>
														</tr>
														<tr class="bottom">
															<td class="col-sm-6 kiri kanan">Bonus Insentif Listrik</td>
															<td class="col-sm-3 text-right kanan bonus_insentif_listrik" data-val="<?php echo $total_bonus_insentif_listrik; ?>"><?php echo angkaRibuan($total_bonus_insentif_listrik); ?></td>
															<td class="col-sm-3 text-right kanan">-</td>
														</tr>
														<tr class="bottom">
															<td class="col-sm-6 kiri kanan text-right"><b>TOTAL</b></td>
															<td class="col-sm-3 text-right kanan total_pemasukan" data-val="<?php echo $total_pemasukan; ?>"><b><?php echo angkaRibuan($total_pemasukan); ?></b></td>
															<td class="col-sm-3 text-right kanan total_pengeluaran" data-val="<?php echo $total_pengeluaran; ?>"><b><?php echo angkaRibuan($total_pengeluaran); ?></b></td>
														</tr>
													</tbody>
												</table>
											</small>
										</fieldset>
									</div>
									<div class="col-sm-6" style="padding-right: 0px;">
										<fieldset>
											<legend>Performance Peternak</legend>
											<small>
												<table class="table table-nobordered" style="margin-bottom: 0px;">
													<tbody>
														<tr class="top">
															<td class="col-sm-8 kiri kanan">Jumlah Panen (Ekor)</td>
															<td class="col-sm-4 kanan text-right jml_ekor_panen" data-val="<?php echo $total_ekor; ?>"><?php echo angkaRibuan($total_ekor); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">Berat Badan (Kg)</td>
															<td class="col-sm-4 kanan text-right jml_panen_kg" data-val="<?php echo $total_tonase; ?>"><?php echo angkaDecimal($total_tonase); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">BB Rata-Rata / Ekor (Kg)</td>
															<td class="col-sm-4 kanan text-right bb_panen" data-val="<?php echo $bb; ?>"><?php echo angkaDecimal($bb); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">FCR</td>
															<td class="col-sm-4 kanan text-right fcr" data-val="<?php echo $fcr; ?>"><?php echo angkaDecimal($fcr); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">Deplesi</td>
															<td class="col-sm-4 kanan text-right deplesi" data-val="<?php echo $deplesi; ?>"><?php echo angkaDecimal($deplesi); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">Rata-Rata Umur</td>
															<td class="col-sm-4 kanan text-right rata_umur" data-val="<?php echo $rata_umur_panen; ?>"><?php echo angkaDecimal($rata_umur_panen); ?></td>
														</tr>
														<tr class="bottom">
															<td class="col-sm-8 kiri kanan"><b>IP</b></td>
															<td class="col-sm-4 kanan text-right ip" data-val="<?php echo $ip; ?>"><b><?php echo angkaDecimal($ip); ?></b></td>
														</tr>
													</tbody>
												</table>
											</small>
										</fieldset>
									</div>
								</div>
							</form>
							<form class="form-horizontal" role="form">
				            	<div class="form-group">
					                <div class="col-sm-6" style="padding-left: 0px;">
										<fieldset>
											<legend>Potongan Peternak</legend>
											<small>
												<table class="table table-nobordered potongan" style="margin-bottom: 0px;">
													<tbody>
														<!-- <?php $total_potongan = 0; $idx = 0; ?>
														<?php if ( !empty($data_plasma['detail']['data_potongan']) ): ?>
															<?php $dp = $data_plasma['detail']['data_potongan']; ?>
															<?php foreach ($dp as $k_dp => $v_dp): ?>
																<?php if ( $v_dp['sudah_bayar'] > 0 ): ?>
																	<tr class="potongan <?php echo ($idx == 0) ? 'top' : ''; ?>" data-idtrans="<?php echo $v_dp['id_jual']; ?>">
																		<td class="col-sm-5 kiri kanan keterangan" data-val="<?php echo $v_dp['keterangan']; ?>"><?php echo $v_dp['keterangan']; ?></td>
																		<td class="col-sm-4 text-right kanan jumlah_tagihan" data-tagihan="<?php echo $v_dp['tagihan']; ?>" data-bayar="<?php echo $v_dp['sudah_bayar']; ?>">
																			<?php echo angkaDecimal($v_dp['sudah_bayar']); ?>
																		</td>

																		<?php $total_potongan += $v_dp['sudah_bayar']; ?>
																	</tr>
																	<?php $idx++; ?>
																<?php endif ?>
															<?php endforeach ?>
														<?php endif ?>
														<tr class="top_bottom">
															<td class="col-sm-5 kiri kanan text-right"><b>TOTAL</b></td>
															<td class="col-sm-4 kanan text-right total_potongan" data-val="<?php echo $total_potongan; ?>"><b><?php echo angkaDecimal($total_potongan); ?></b></td>
														</tr>

														<?php $total_pengeluaran += $total_potongan; ?> -->
														<?php $total_potongan = 0; $idx = 0; ?>
														<?php if ( empty($id) ): ?>
															<?php if ( !empty($data_plasma['detail']['data_potongan']) ): ?>
																<?php $dp = $data_plasma['detail']['data_potongan']; ?>
																<?php foreach ($dp as $k_dp => $v_dp): ?>
																	<?php if ( $v_dp['sudah_bayar'] > 0 ): ?>
																		<?php if ( !empty($v_dp['id_jual']) ): ?>
																			<tr class="potongan <?php echo ($idx == 0) ? 'top' : ''; ?>" data-idtrans="<?php echo $v_dp['id_jual']; ?>">
																				<td class="col-sm-5 kiri kanan keterangan" data-val="<?php echo $v_dp['keterangan']; ?>"><?php echo $v_dp['keterangan']; ?></td>
																				<td class="col-sm-4 text-right kanan jumlah_tagihan" data-tagihan="<?php echo $v_dp['tagihan']; ?>" data-bayar="<?php echo $v_dp['sudah_bayar']; ?>">
																					<?php echo angkaDecimal($v_dp['sudah_bayar']); ?>
																				</td>
																			</tr>
																		<?php else: ?>
																			<tr class="non_potongan_peralatan <?php echo ($idx == 0) ? 'top' : ''; ?>">
																				<td class="col-sm-5 kiri kanan">
																					<input type="text" class="form-control ket_potongan" placeholder="Keterangan" value="<?php echo $v_dp['keterangan']; ?>">
																				</td>
																				<td class="col-sm-4 text-right kanan" data-val="">
																					<input type="text" class="form-control text-right jumlah_bayar" data-tipe="decimal" placeholder="Jumlah" onblur="rg.hit_tot_potongan(this)" value="<?php echo angkaDecimal($v_dp['sudah_bayar']); ?>">
																				</td>
																				<td class="col-sm-3 text-center kanan">
																					<button type="button" class="btn btn-primary" onclick="rg.add_row(this)"><i class="fa fa-plus"></i></button>
																					<button type="button" class="btn btn-danger" onclick="rg.remove_row(this)"><i class="fa fa-times"></i></button>
																				</td>
																			</tr>
																		<?php endif ?>
																		<?php $total_potongan += $v_dp['sudah_bayar']; ?>
																		<?php $idx++; ?>
																	<?php endif ?>
																<?php endforeach ?>
															<?php else: ?>
																<tr class="non_potongan_peralatan <?php echo ($idx == 0) ? 'top' : ''; ?>">
																	<td class="col-sm-5 kiri kanan">
																		<input type="text" class="form-control ket_potongan" placeholder="Keterangan">
																	</td>
																	<td class="col-sm-4 text-right kanan" data-val="">
																		<input type="text" class="form-control text-right jumlah_bayar" data-tipe="decimal" placeholder="Jumlah" onblur="rg.hit_tot_potongan()">
																	</td>
																	<td class="col-sm-3 text-center kanan">
																		<button type="button" class="btn btn-primary" onclick="rg.add_row(this)"><i class="fa fa-plus"></i></button>
																		<button type="button" class="btn btn-danger" onclick="rg.remove_row(this)"><i class="fa fa-times"></i></button>
																	</td>
																</tr>
															<?php endif ?>
															<tr class="top_bottom">
																<td class="col-sm-5 kiri kanan text-right"><b>TOTAL</b></td>
																<td class="col-sm-4 kanan text-right total_potongan" data-val="<?php echo $total_potongan; ?>"><b><?php echo angkaDecimal($total_potongan); ?></b></td>
																<td class="col-sm-4 kanan text-right"></td>
															</tr>
														<?php else: ?>
															<?php if ( !empty($data_plasma['detail']['data_potongan']) ): ?>
																<?php $dp = $data_plasma['detail']['data_potongan']; ?>
																<?php foreach ($dp as $k_dp => $v_dp): ?>
																	<?php if ( $v_dp['sudah_bayar'] > 0 ): ?>
																		<tr class="potongan <?php echo ($idx == 0) ? 'top' : ''; ?>" data-idtrans="<?php echo $v_dp['id_jual']; ?>">
																			<td class="col-sm-5 kiri kanan keterangan" data-val="<?php echo $v_dp['keterangan']; ?>"><?php echo $v_dp['keterangan']; ?></td>
																			<td class="col-sm-4 text-right kanan jumlah_tagihan" data-tagihan="<?php echo $v_dp['tagihan']; ?>" data-bayar="<?php echo $v_dp['sudah_bayar']; ?>">
																				<?php echo angkaDecimal($v_dp['sudah_bayar']); ?>
																			</td>

																			<?php $total_potongan += $v_dp['sudah_bayar']; ?>
																		</tr>
																		<?php $idx++; ?>
																	<?php endif ?>
																<?php endforeach ?>
															<?php endif ?>
															<tr class="top_bottom">
																<td class="col-sm-5 kiri kanan text-right"><b>TOTAL</b></td>
																<td class="col-sm-4 kanan text-right total_potongan" data-val="<?php echo $total_potongan; ?>"><b><?php echo angkaDecimal($total_potongan); ?></b></td>
															</tr>
														<?php endif ?>

														<?php $total_pengeluaran += $total_potongan; ?>
													</tbody>
												</table>
											</small>
										</fieldset>
									</div>
									<div class="col-sm-6" style="padding-right: 0px;">
										<fieldset>
											<legend>Bonus Tambahan Peternak</legend>
											<small>
												<table class="table table-nobordered bonus" style="margin-bottom: 0px;">
													<tbody>
														<!-- <?php $total_bonus = 0; ?>
														<?php if ( !empty($data_plasma['detail']['data_bonus']) ): ?>
															<?php foreach ($data_plasma['detail']['data_bonus'] as $k_db => $v_db): ?>
																<tr class="bonus" data-idtrans="<?php echo $v_db['id_trans']; ?>">
																	<td class="col-sm-5 kiri kanan keterangan" data-val="<?php echo $v_db['keterangan']; ?>"><?php echo $v_db['keterangan']; ?></td>
																	<td class="col-sm-4 text-right kanan jumlah_bonus" data-val="<?php echo $v_db['jumlah']; ?>">
																		<?php echo angkaDecimal($v_db['jumlah']); ?>
																	</td>
																</tr>

																<?php $total_bonus += $v_db['jumlah']; ?>
															<?php endforeach ?>
														<?php endif ?>
														<tr class="top_bottom">
															<td class="col-sm-5 kiri kanan text-right"><b>TOTAL</b></td>
															<td class="col-sm-4 kanan text-right total_bonus" data-val="<?php echo $total_bonus; ?>"><b><?php echo angkaDecimal($total_bonus); ?></b></td>
														</tr> -->
														<?php $total_bonus = 0; ?>
														<?php if ( empty($id) ): ?>
															<?php if ( !empty($data_plasma['detail']['data_bonus']) ): ?>
																<?php foreach ($data_plasma['detail']['data_bonus'] as $k_db => $v_db): ?>
																	<tr class="bonus">
																		<td class="col-sm-5 kiri kanan">
																			<input type="text" class="form-control ket_bonus" placeholder="Keterangan" value="<?php echo $v_db['keterangan']; ?>">
																		</td>
																		<td class="col-sm-4 text-right kanan" data-val="">
																			<input type="text" class="form-control text-right jumlah_bonus" data-tipe="decimal" placeholder="Jumlah" onblur="rg.hit_tot_bonus(this)" value="<?php echo angkaDecimal($v_db['jumlah']); ?>">
																		</td>
																		<td class="col-sm-3 text-center kanan">
																			<button type="button" class="btn btn-primary" onclick="rg.add_row(this)"><i class="fa fa-plus"></i></button>
																			<button type="button" class="btn btn-danger" onclick="rg.remove_row_bonus(this)"><i class="fa fa-times"></i></button>
																		</td>
																	</tr>
																	<?php $total_bonus += $v_db['jumlah']; ?>
																<?php endforeach ?>
															<?php else: ?>
																<tr class="bonus">
																	<td class="col-sm-5 kiri kanan">
																		<input type="text" class="form-control ket_bonus" placeholder="Keterangan">
																	</td>
																	<td class="col-sm-4 text-right kanan" data-val="">
																		<input type="text" class="form-control text-right jumlah_bonus" data-tipe="decimal" placeholder="Jumlah" onblur="rg.hit_tot_bonus()">
																	</td>
																	<td class="col-sm-3 text-center kanan">
																		<button type="button" class="btn btn-primary" onclick="rg.add_row(this)"><i class="fa fa-plus"></i></button>
																		<button type="button" class="btn btn-danger" onclick="rg.remove_row_bonus(this)"><i class="fa fa-times"></i></button>
																	</td>
																</tr>
															<?php endif ?>
															<tr class="top_bottom">
																<td class="col-sm-5 kiri kanan text-right"><b>TOTAL</b></td>
																<td class="col-sm-4 kanan text-right total_bonus" data-val="<?php echo $total_bonus; ?>"><b><?php echo angkaDecimal($total_bonus); ?></b></td>
																<td class="col-sm-4 kanan text-right"></td>
															</tr>
														<?php else: ?>
															<?php if ( !empty($data_plasma['detail']['data_bonus']) ): ?>
																<?php foreach ($data_plasma['detail']['data_bonus'] as $k_db => $v_db): ?>
																	<tr class="bonus" data-idtrans="<?php echo $v_db['id_trans']; ?>">
																		<td class="col-sm-5 kiri kanan keterangan" data-val="<?php echo $v_db['keterangan']; ?>"><?php echo $v_db['keterangan']; ?></td>
																		<td class="col-sm-4 text-right kanan jumlah_bonus" data-val="<?php echo $v_db['jumlah']; ?>">
																			<?php echo angkaDecimal($v_db['jumlah']); ?>
																		</td>
																	</tr>

																	<?php $total_bonus += $v_db['jumlah']; ?>
																<?php endforeach ?>
															<?php endif ?>
															<tr class="top_bottom">
																<td class="col-sm-5 kiri kanan text-right"><b>TOTAL</b></td>
																<td class="col-sm-4 kanan text-right total_bonus" data-val="<?php echo $total_bonus; ?>"><b><?php echo angkaDecimal($total_bonus); ?></b></td>
															</tr>
														<?php endif ?>

														<?php $total_pemasukan += $total_bonus; ?>
													</tbody>
												</table>
											</small>
										</fieldset>
									</div>
								</div>
							</form>
							<form class="form-horizontal" role="form">
				            	<div class="form-group">
						            <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px;">
						            	<div class="col-sm-6" style="padding-left: 0px;">
							            	<table class="table table-nobordered" style="margin-bottom: 0px;">
							            		<tbody>
							            			<tr>
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;"><b>Pendapatan Peternak Sebelum Pajak</b></td>
							            				<?php $pendapatan_peternak_rhpp_plasma = $total_pemasukan - $total_pengeluaran; ?>
							            				<td class="col-sm-4 text-right pendapatan_peternak" data-val="<?php echo $pendapatan_peternak_rhpp_plasma; ?>" style="padding-bottom: 0px;">
							            					<b>
							            						<?php if ( $pendapatan_peternak_rhpp_plasma > 0 ): ?>
						            								<?php echo angkaRibuan($pendapatan_peternak_rhpp_plasma); ?>
						            							<?php else: ?>
						            								<?php echo '('.angkaRibuan(abs($pendapatan_peternak_rhpp_plasma)).')'; ?>
						            							<?php endif ?>
							            					</b>
							            				</td>
							            			</tr>
							            			<?php $nilai_potongan_pajak = ($data['potongan_pajak'] > 0) ? ($total_pemasukan - $total_pengeluaran) * ($data['potongan_pajak']/100) : 0; ?>
							            			<tr>
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;">Potongan Pajak <span class="prs_pajak" data-val="<?php echo $data['potongan_pajak']; ?>"><?php echo angkaDecimal($data['potongan_pajak']); ?>%</span> (PPh Pasal 23)</td>
							            				<td class="col-sm-4 text-right nilai_potongan_pajak" data-val="<?php echo $nilai_potongan_pajak; ?>" style="padding-bottom: 0px;"><?php echo angkaRibuan( $nilai_potongan_pajak ); ?></td>
							            			</tr>
							            			<tr>
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;"><b>Pendapatan Peternak Setelah Kena Pajak</b></td>
							            				<td class="col-sm-4 text-right pendapatan_peternak_setelah_pajak" data-val="<?php echo (($total_pemasukan - $total_pengeluaran) - $nilai_potongan_pajak); ?>" style="padding-bottom: 0px;">
							            					<b>
							            						<?php if ( (($total_pemasukan - $total_pengeluaran) - $nilai_potongan_pajak) > 0 ): ?>
							            							<?php echo angkaRibuan( ($total_pemasukan - $total_pengeluaran) - $nilai_potongan_pajak ); ?>
							            						<?php else: ?>
							            							<?php echo '('.angkaRibuan( abs(($total_pemasukan - $total_pengeluaran) - $nilai_potongan_pajak) ).')'; ?>
							            						<?php endif ?>
							            					</b>
							            				</td>
							            			</tr>
							            		</tbody>
							            	</table>
						            	</div>
						            	<?php if ( !empty($id) ): ?>
							            	<div class="col-sm-6 no-padding">
							            		<div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; padding-top: 10px;">
								            		<button type="button" class="btn btn-default pull-right" onclick="rg.print(this)" data-id="<?php echo exEncrypt($id); ?>"><i class="fa fa-print"></i> Print</button>
							            		</div>
							            		<div class="col-sm-12" style="padding-left: 0px; padding-right: 0px; padding-top: 10px;">
								            		<button type="button" class="btn btn-default pull-right" onclick="rg.export_excel_plasma(this)" data-id="<?php echo exEncrypt($id); ?>"><i class="fa fa-file-excel-o"></i> Export Excel Plasma</button>
							            		</div>
							            	</div>
							            <?php endif ?>
						            </div>
						    	</div>
						    </form>
				        </div>
						<div id="rhpp_inti" class="tab-pane fade <?php echo $active_inti_div; ?>" style="<?php echo $style_hide_inti; ?>">
							<form class="form-horizontal" role="form">
				                <div class="form-group">
				                	<div class="col-md-1 no-padding">
				                		<label class="control-label">Mitra</label>
				                	</div>
				                	<div class="col-md-3 no-padding">
				                        <label class="control-label mitra" data-val="<?php echo $data['mitra']; ?>" data-nomor="<?php echo $data['nomor']; ?>">: <?php echo $data['mitra']; ?></label>
				                	</div>
				                	<div class="col-md-1 no-padding">&nbsp;</div>
				                	<div class="col-md-2 no-padding">
				                		<label class="control-label">Total Populasi</label>
				                	</div>
				                	<div class="col-md-3 no-padding">
				                        <label class="control-label tot_populasi" data-val="<?php echo $data['tot_populasi']; ?>">: <?php echo angkaRibuan($data['tot_populasi']); ?></label>
				                	</div>
				                </div>
				                <div class="form-group"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				                <div class="form-group">
				                	<table class="table table-nobordered tbl_list_noreg" style="margin-bottom: 0px;">
				                		<thead>
				                			<tr>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>No. Reg</b>
				                						</div>
				                					</div>
				                				</td>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>Kandang</b>
				                						</div>
				                					</div>
				                				</td>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>Populasi</b>
				                						</div>
				                					</div>
				                				</td>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>Tgl Chick In</b>
				                						</div>
				                					</div>
				                				</td>
				                				<td class="col-md-1 no-padding" style="padding: 0px;">
				                					<div class="col-md-12" style="padding-top: 0px; padding-left: 0px; padding-bottom: 0px; margin-right: 10px;">
				                						<div class="col-md-12 no-padding" style="border-bottom: 1px solid black;">
				                							<b>Tgl Tutup Siklus</b>
				                						</div>
				                					</div>
				                				</td>
				                			</tr>
				                		</thead>
				                		<tbody>
				                			<?php foreach ($data['detail'] as $k_detail => $v_detail): ?>
					                			<tr>
					                				<td class="noreg" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['noreg']; ?>"><?php echo $v_detail['noreg']; ?></td>
					                				<td class="kandang" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['kandang']; ?>"><?php echo (int) $v_detail['kandang']; ?></td>
					                				<td class="populasi" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['populasi']; ?>"><?php echo angkaRibuan($v_detail['populasi']); ?></td>
					                				<td class="tgl_docin" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['tgl_docin']; ?>"><?php echo tglIndonesia($v_detail['tgl_docin'], '-', ' '); ?></td>
					                				<td class="tgl_tutup" style="border: none; padding: 0px;" data-val="<?php echo $v_detail['tgl_tutup']; ?>"><?php echo tglIndonesia($v_detail['tgl_tutup'], '-', ' '); ?></td>
					                			</tr>
				                			<?php endforeach ?>
				                		</tbody>
				                	</table>
				                </div>
				            </form>
				            <br>
				            <?php
				            	$populasi = $data['tot_populasi'];
				            	$rata_umur_panen = $data['rata_umur_panen'];

				            	$total_nilai_doc = 0;
				            	$total_nilai_pakan = 0;
				            	$total_nilai_pemakaian = 0;

				            	$total_jumlah_pakan = 0;
				            ?>
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
				                	<small>
					                	<table class="table table-bordered pemakaian">
					                		<thead>
					                			<tr>
					                				<th class="col-sm-1 text-center">Tanggal</th>
					                				<th class="col-sm-1 text-center">Nota / SJ</th>
					                				<th class="col-sm-2 text-center">Barang</th>
					                				<th class="col-sm-1 text-center">Box / Sak</th>
					                				<th class="col-sm-1 text-center">Jumlah</th>
					                				<th class="col-sm-1 text-center">Harga</th>
					                				<th class="col-sm-2 text-center">Total</th>
					                			</tr>
					                		</thead>
					                		<tbody>
					                			<?php $total_pemakaian = 0; ?>
					                			<tr class="head">
					                				<td colspan=7"><b>DOC</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_doc']) ): ?>
					                				<?php 
					                					$data_doc = $data_inti['detail']['data_doc']['doc'];
					                					$total_box = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php if ( !empty($data_doc) ): ?>
					                					<?php foreach ($data_doc as $k_doc => $v_doc): ?>
								                			<tr class="data_doc">
								                				<td class="text-left tanggal" data-val="<?php echo $v_doc['tgl_docin']; ?>"><?php echo tglIndonesia($v_doc['tgl_docin'], '-', ' '); ?></td>
								                				<td class="text-center nota" data-val="<?php echo $v_doc['sj']; ?>"><?php echo $v_doc['sj']; ?></td>
								                				<td class="barang" data-val="<?php echo $v_doc['barang']; ?>"><?php echo $v_doc['barang']; ?></td>
								                				<td class="text-right box_zak" data-val="<?php echo $v_doc['box']; ?>"><?php echo angkaRibuan($v_doc['box']); ?></td>
								                				<td class="text-right jumlah" data-val="<?php echo $v_doc['jumlah']; ?>"><?php echo angkaRibuan($v_doc['jumlah']); ?></td>
								                				<td class="text-right harga" data-val="<?php echo $v_doc['harga']; ?>"><?php echo angkaRibuan($v_doc['harga']); ?></td>
								                				<td class="text-right total" data-val="<?php echo $v_doc['total']; ?>"><?php echo angkaRibuan($v_doc['total']); ?></td>
								                			</tr>
								                			<?php
								                				$total_box += $v_doc['box'];
							                					$total_jumlah += $v_doc['jumlah'];
							                					$total_nilai += $v_doc['total'];
								                			?>
					                					<?php endforeach ?>
					                				<?php endif ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_box); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php $total_nilai_doc = $total_nilai; ?>
					                			<?php endif ?>

					                			<?php $total_pemakaian_zak = 0; ?>
					                			<?php $total_pemakaian_jumlah = 0; ?>
					                			<?php $total_pemakaian_nilai = 0; ?>
					                			<tr class="head">
					                				<td colspan="7"><b>PAKAN</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_pakan']) ): ?>
					                				<?php 
					                					$data_pakan = $data_inti['detail']['data_pakan']; 
					                					$total_zak = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_pakan as $k => $val): ?>
					                					<tr class="data_pakan">
							                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo empty($val['sj']) ? null : $val['sj']; ?>"><?php echo empty($val['sj']) ? '-' : $val['sj']; ?></td>
							                				<td class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<td class="text-right box_zak" data-val="<?php echo $val['zak']; ?>"><?php echo angkaRibuan($val['zak']); ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaRibuan($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaRibuan($val['harga']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php
							                				$total_zak += $val['zak'];
						                					$total_jumlah += $val['jumlah'];
						                					$total_nilai += $val['total'];
							                			?>
							                			<?php $total_pemakaian_zak += $val['zak']; ?>
							                			<?php $total_pemakaian_jumlah += $val['jumlah']; ?>
							                			<?php $total_pemakaian_nilai += $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL PENGIRIMAN</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php 
					                					$total_nilai_pakan += $total_nilai; 
					                					$total_jumlah_pakan += $total_jumlah;
					                				?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>ONGKOS ANGKUT PAKAN</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_oa_pakan']) ): ?>
					                				<?php 
					                					$data_oa_pakan = $data_inti['detail']['data_oa_pakan']; 
					                					$total_zak = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_oa_pakan as $k_dop => $v_dop): ?>
					                					<tr class="data_oa_pakan">
							                				<td class="text-left tanggal" data-val="<?php echo $v_dop['tanggal']; ?>"><?php echo tglIndonesia($v_dop['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nopol" data-val="<?php echo strtoupper($v_dop['nopol']); ?>" data-nota="<?php echo $k; ?>"><?php echo strtoupper($v_dop['nopol']); ?></td>
							                				<td class="barang" data-val="<?php echo $v_dop['barang']; ?>"><?php echo $v_dop['barang']; ?></td>
							                				<td class="text-right box_zak" data-val="<?php echo $v_dop['zak']; ?>"><?php echo angkaRibuan($v_dop['zak']); ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $v_dop['jumlah']; ?>"><?php echo angkaRibuan($v_dop['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $v_dop['harga']; ?>"><?php echo angkaRibuan($v_dop['harga']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $v_dop['total']; ?>"><?php echo angkaRibuan($v_dop['total']); ?></td>
							                			</tr>
							                			<?php
							                				$total_zak += $v_dop['zak'];
						                					$total_jumlah += $v_dop['jumlah'];
						                					$total_nilai += $v_dop['total'];
							                			?>
							                			<?php $total_pemakaian_nilai += $v_dop['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL ONGKOS ANGKUT</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php 
					                					$total_nilai_pakan += $total_nilai; 
					                				?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>PINDAH PAKAN</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_pindah_pakan']) ): ?>
					                				<?php 
					                					$data_pindah_pakan = $data_inti['detail']['data_pindah_pakan']; 
					                					$total_zak = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_pindah_pakan as $k => $val): ?>
					                					<tr class="data_pindah_pakan">
							                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo empty($val['sj']) ? null : $val['sj']; ?>"><?php echo empty($val['sj']) ? '-' : $val['sj']; ?></td>
							                				<td class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<td class="text-right box_zak" data-val="<?php echo $val['zak']; ?>"><?php echo angkaRibuan($val['zak']); ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaRibuan($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaRibuan($val['harga']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php
							                				$total_zak += $val['zak'];
						                					$total_jumlah += $val['jumlah'];
						                					$total_nilai += $val['total'];
							                			?>
							                			<?php $total_pemakaian_zak -= $val['zak']; ?>
							                			<?php $total_pemakaian_jumlah -= $val['jumlah']; ?>
							                			<?php $total_pemakaian_nilai -= $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL PENGIRIMAN</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php 
					                					$total_nilai_pakan -= $total_nilai; 
					                					$total_jumlah_pakan -= $total_jumlah;
					                				?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>ONGKOS ANGKUT PINDAH PAKAN</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_oa_pindah_pakan']) ): ?>
					                				<?php 
					                					$data_oa_pindah_pakan = $data_inti['detail']['data_oa_pindah_pakan']; 
					                					$total_zak = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_oa_pindah_pakan as $k_dop => $v_dop): ?>
					                					<tr class="data_oa_pindah_pakan">
							                				<td class="text-left tanggal" data-val="<?php echo $v_dop['tanggal']; ?>"><?php echo tglIndonesia($v_dop['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nopol" data-val="<?php echo strtoupper($v_dop['nopol']); ?>" data-nota="<?php echo $k; ?>"><?php echo strtoupper($v_dop['nopol']); ?></td>
							                				<td class="barang" data-val="<?php echo $v_dop['barang']; ?>"><?php echo $v_dop['barang']; ?></td>
							                				<td class="text-right box_zak" data-val="<?php echo $v_dop['zak']; ?>"><?php echo angkaRibuan($v_dop['zak']); ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $v_dop['jumlah']; ?>"><?php echo angkaRibuan($v_dop['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $v_dop['harga']; ?>"><?php echo angkaRibuan($v_dop['harga']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $v_dop['total']; ?>"><?php echo angkaRibuan($v_dop['total']); ?></td>
							                			</tr>
							                			<?php
							                				$total_zak += $v_dop['zak'];
						                					$total_jumlah += $v_dop['jumlah'];
						                					$total_nilai += $v_dop['total'];
							                			?>
							                			<?php $total_pemakaian_nilai -= $v_dop['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL ONGKOS ANGKUT PINDAH PAKAN</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php 
					                					$total_nilai_pakan -= $total_nilai; 
					                					// $total_jumlah_pakan += $total_jumlah;
					                				?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>RETUR PAKAN</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_retur_pakan']) ): ?>
					                				<?php 
					                					$data_retur_pakan = $data_inti['detail']['data_retur_pakan'];
					                					$total_zak = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_retur_pakan as $k => $val): ?>
					                					<tr class="data_retur_pakan">
							                				<td class="text-left" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo $val['no_retur']; ?>"><?php echo $val['no_retur']; ?></td>
							                				<td class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<?php $zak = ($val['jumlah'] > 0) ? $val['jumlah']/50 : 0; ?>
							                				<td class="text-right box_zak" data-val="<?php echo $zak; ?>"><?php echo angkaRibuan($zak); ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaDecimal($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaRibuan($val['harga']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php $total_zak += $zak; ?>
							                			<?php $total_nilai += $val['total']; ?>
							                			<?php $total_jumlah += $val['jumlah']; ?>

							                			<?php $total_pemakaian_zak -= $zak; ?>
														<?php $total_pemakaian_jumlah -= $val['jumlah']; ?>
														<?php $total_pemakaian_nilai -= $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL RETUR</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php $total_nilai_pakan -= $total_nilai; ?>
					                				<?php $total_jumlah_pakan -= $total_jumlah; ?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>ONGKOS ANGKUT RETUR PAKAN</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_oa_retur_pakan']) ): ?>
					                				<?php 
					                					$data_oa_retur_pakan = $data_inti['detail']['data_oa_retur_pakan'];
					                					$total_zak = 0;
					                					$total_jumlah = 0;
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_oa_retur_pakan as $k_dorp => $v_dorp): ?>
						                				<?php foreach ($v_dorp as $k => $val): ?>
						                					<tr class="data_oa_retur_pakan">
								                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
								                				<td class="text-center nopol" data-val="<?php echo strtoupper($val['nopol']); ?>" data-nota="<?php echo $k; ?>"><?php echo strtoupper($val['nopol']); ?></td>
								                				<td class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
								                				<?php $zak = ($val['jumlah'] > 0) ? $val['jumlah']/50 : 0; ?>
								                				<td class="text-right box_zak" data-val="<?php echo $zak; ?>"><?php echo angkaRibuan($zak); ?></td>
								                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaDecimal($val['jumlah']); ?></td>
								                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaRibuan($val['harga']); ?></td>
								                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
								                			</tr>
								                			<?php $total_zak += $zak; ?>
								                			<?php $total_nilai += $val['total']; ?>
								                			<?php $total_jumlah += $val['jumlah']; ?>

															<?php $total_pemakaian_nilai -= $val['total']; ?>
						                				<?php endforeach ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="3"><b>TOTAL ONGKOS ANGKUT RETUR</b></td>
					                					<td class="text-right"><b><?php echo angkaRibuan($total_zak); ?></b></td>
					                					<td class="text-right"><b><?php echo angkaDecimal($total_jumlah) ?></b></td>
					                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php 
					                					$total_nilai_pakan -= $total_nilai;
					                				?>
					                			<?php endif ?>

					                			<tr>
				                					<td class="text-right" colspan="3"><b>TOTAL PEMAKAIAN</b></td>
				                					<td class="text-right"><b><?php echo angkaRibuan($total_pemakaian_zak); ?></b></td>
				                					<td class="text-right"><b><?php echo angkaDecimal($total_pemakaian_jumlah) ?></b></td>
				                					<td class="text-right" colspan="2"><b><?php echo angkaRibuan($total_pemakaian_nilai); ?></b></td>
				                				</tr>

					                			<tr class="head">
					                				<td colspan="7"><b>OVK</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_voadip']) ): ?>
					                				<?php 
					                					$data_voadip = $data_inti['detail']['data_voadip'];
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_voadip as $k => $val): ?>
					                					<tr class="data_voadip">
							                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo $val['sj']; ?>"><?php echo $val['sj']; ?></td>
							                				<td colspan="2" class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaDecimal($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaDecimalFormat($val['harga'], $val['decimal']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php $total_nilai += $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="4"><b>TOTAL PENGIRIMAN</b></td>
					                					<td class="text-right" colspan="3"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php $total_pemakaian += $total_nilai; ?>
					                			<?php endif ?>

					                			<tr class="head">
					                				<td colspan="7"><b>RETUR OVK</b></td>
					                			</tr>
					                			<?php if ( !empty($data_inti['detail']['data_retur_voadip']) ): ?>
					                				<?php 
					                					$data_retur_voadip = $data_inti['detail']['data_retur_voadip'];
					                					$total_nilai = 0;
					                				?>
					                				<?php foreach ($data_retur_voadip as $k => $val): ?>
					                					<tr class="data_retur_voadip">
							                				<td class="text-left tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
							                				<td class="text-center nota" data-val="<?php echo $val['no_retur']; ?>"><?php echo $val['no_retur']; ?></td>
							                				<td colspan="2" class="barang" data-val="<?php echo $val['barang']; ?>"><?php echo $val['barang']; ?></td>
							                				<td class="text-right jumlah" data-val="<?php echo $val['jumlah']; ?>"><?php echo angkaDecimal($val['jumlah']); ?></td>
							                				<td class="text-right harga" data-val="<?php echo $val['harga']; ?>"><?php echo angkaDecimalFormat($val['harga'], $val['decimal']); ?></td>
							                				<td class="text-right total" data-val="<?php echo $val['total']; ?>"><?php echo angkaRibuan($val['total']); ?></td>
							                			</tr>
							                			<?php $total_nilai += $val['total']; ?>
					                				<?php endforeach ?>
					                				<tr>
					                					<td class="text-right" colspan="4"><b>TOTAL RETUR</b></td>
					                					<td class="text-right" colspan="3"><b><?php echo angkaRibuan($total_nilai); ?></b></td>
					                				</tr>
					                				<?php $total_pemakaian -= $total_nilai; ?>
					                			<?php endif ?>

					                			<tr>
				                					<td class="text-right" colspan="4"><b>TOTAL PEMAKAIAN</b></td>
				                					<td class="text-right" colspan="3"><b><?php echo angkaRibuan($total_pemakaian); ?></b></td>
				                				</tr>
				                				<?php $total_nilai_pemakaian = $total_pemakaian; ?>
					                		</tbody>
					                	</table>
					                </small>
				                </div>
				            </form>
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
				                	<hr style="margin-top: 0px;">
				                </div>
				            </form>
				            <?php
								$total_ekor = 0;
								$total_tonase = 0;
								$total_nilai_kontrak = 0;
								$total_nilai_pasar = 0;
								// $total_nilai_insentif = 0;

								$total_pembelian_sapronak = $total_nilai_doc + $total_nilai_pakan + $total_nilai_pemakaian;
								$bonus_kematian = 0;
								$bonus_insentif_fcr = 0;
				            ?>
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
									<fieldset>
										<legend>Penjualan Ayam Peternak</legend>
										<small>
											<table class="table table-bordered penjualan_ayam" style="margin-bottom: 0px;">
												<thead>
													<tr>
														<th class="col-sm-1 text-center">Tanggal</th>
														<th class="col-sm-1 text-center">DO</th>
														<th class="col-sm-2 text-center">Pembeli</th>
														<th class="col-sm-1 text-center">Ekor</th>
														<th class="col-sm-1 text-center">Tonase (Kg)</th>
														<th class="text-center">BB Rata2</th>
														<th class="text-center">Hrg Pasar</th>
														<th class="col-sm-1 text-center">Total</th>
													</tr>
												</thead>
												<tbody>
													<?php if ( !empty($data_inti['detail']['data_rpah']) ): ?>
														<?php $data_rpah = $data_inti['detail']['data_rpah']; ?>
														<?php foreach ($data_rpah as $k => $val): ?>
															<tr class="data_penjualan">
																<td class="tanggal" data-val="<?php echo $val['tanggal']; ?>"><?php echo tglIndonesia($val['tanggal'], '-', ' '); ?></td>
																<td class="text-center nota" data-val="<?php echo $val['do'] ?>"><?php echo $val['do'] ?></td>
																<td class="pembeli" data-val="<?php echo $val['pembeli'] ?>"><?php echo $val['pembeli'] ?></td>
																<td class="text-right ekor" data-val="<?php echo $val['ekor']; ?>"><?php echo angkaRibuan($val['ekor']); ?></td>
																<td class="text-right tonase" data-val="<?php echo $val['tonase']; ?>"><?php echo angkaDecimal($val['tonase']); ?></td>
																<td class="text-right bb" data-val="<?php echo $val['bb']; ?>"><?php echo angkaDecimal($val['bb']); ?></td>
																<td class="text-right harga_pasar" data-val="<?php echo $val['hrg_pasar']; ?>"><?php echo angkaRibuan($val['hrg_pasar']); ?></td>
																<td class="text-right total_pasar" data-val="<?php echo $val['total_pasar']; ?>"><?php echo angkaRibuan($val['total_pasar']); ?></td>
															</tr>
															<?php
																$total_ekor += $val['ekor'];
																$total_tonase += $val['tonase'];
																$total_nilai_pasar += $val['total_pasar'];
															?>
														<?php endforeach ?>
														<tr>
															<td class="text-right" colspan="3"><b>TOTAL</b></td>
															<td class="text-right"><b><?php echo angkaRibuan($total_ekor); ?></b></td>
															<td class="text-right"><b><?php echo angkaDecimal($total_tonase); ?></b></td>
															<td colspan="2"></td>
															<td class="text-right"><b><?php echo angkaRibuan($total_nilai_pasar); ?></b></td>
														</tr>
														<?php 
															// $fcr = 0;
															// if ( $total_jumlah_pakan > 0 && $total_tonase > 0 ) {
															// 	$fcr = $total_jumlah_pakan / $total_tonase;
															// } 
															$fcr = $data['fcr'];
														?>
											            <?php 
															// $bb = 0;
															// if ( $total_ekor > 0 && $total_tonase > 0 ) {
															// 	$bb = $total_tonase / $total_ekor;
															// } 
											            	$bb = $data['bb'];
														?>
														<?php
															// $deplesi = 0;
															// if ( $populasi > 0 && $total_ekor > 0 ) {
															// 	$deplesi = abs((($populasi - $total_ekor) / $populasi) * 100);
															// }
															$deplesi = $data['deplesi'];
														?>
														<?php
															// $ip = 0;
															// if ( $deplesi > 0 && $bb > 0 && $fcr > 0 && $rata_umur_panen > 0 ) {
															// 	$ip = ((100 - $deplesi) * $bb) / ($fcr * $rata_umur_panen) * 100;
															// }
															$ip = $data['ip'];
														?>
														<?php
															$bonus_kematian = ($deplesi <= 5) ? 25 * $total_tonase : 0;
															$bonus_insentif_fcr = $data['bonus_insentif_fcr'];
														?>
													<?php else: ?>
														<tr class="text-center" colspan="13">Data tidak ditemukan.</tr>
													<?php endif ?>
												</tbody>
											</table>
										</small>
									</fieldset>
								</div>
				            </form>
				            <form class="form-horizontal" role="form">
				                <div class="form-group">
				                	<hr>
				                </div>
				            </form>
				            <?php
				            	$_cn = !empty($data['cn']) ? $data['cn'] : 0;

								$biaya_opr = $data['biaya_opr'];
				            	$total_pemasukan = $total_nilai_pasar + $_cn;
				            	$pendapatan_peternak_rhpp_plasma = ($pendapatan_peternak_rhpp_plasma > 0) ? $pendapatan_peternak_rhpp_plasma : 0;
				            	$total_pengeluaran = $total_pembelian_sapronak + $data['biaya_materai'] + $biaya_opr + $pendapatan_peternak_rhpp_plasma;

				            	$hide_inti = 'hide';
				            	$hide_internal = null;
				            	if ( $data['jenis_mitra'] == 'ME' ) {
				            		$hide_inti = null;
				            		$hide_internal = 'hide';
				            	}
				            ?>
				            <form class="form-horizontal" role="form">
				            	<div class="form-group">
					                <div class="col-sm-6" style="padding-left: 0px;">
										<fieldset>
											<legend>Rekapitulasi Peternak</legend>
											<small>
												<table class="table table-nobordered" style="margin-bottom: 0px;">
													<tbody>
														<tr class="top">
															<td class="col-sm-6 kiri kanan">Penjualan Ayam</td>
															<td class="col-sm-3 text-right kanan tot_penjualan_ayam" data-val="<?php echo $total_nilai_pasar; ?>"><?php echo angkaRibuan($total_nilai_pasar); ?></td>
															<td class="col-sm-3 text-right kanan">-</td>
														</tr>
														<tr>
															<td class="col-sm-6 kiri kanan">Total Pembelian Sapronak</td>
															<td class="col-sm-3 text-right kanan">-</td>
															<td class="col-sm-3 text-right kanan tot_pembelian_sapronak" data-val="<?php echo $total_pembelian_sapronak; ?>"><?php echo angkaRibuan($total_pembelian_sapronak); ?></td>
														</tr>
														<tr class="<?php echo $hide_inti; ?>">
															<td class="col-sm-6 kiri kanan">Pendapatan Plasma</td>
															<td class="col-sm-3 text-right kanan">-</td>
															<td class="col-sm-3 text-right kanan pendapatan_peternak_form_inti" data-val="<?php echo $pendapatan_peternak_rhpp_plasma; ?>">
																<?php 
																	if ( $pendapatan_peternak_rhpp_plasma > 0 ) {
																		echo angkaRibuan(abs($pendapatan_peternak_rhpp_plasma)); 
																	} else {
																		echo angkaRibuan(abs(0)); 
																	}
																?>
															</td>
														</tr>
														<tr class="<?php echo $hide_inti; ?>">
															<td class="col-sm-6 kiri kanan">Biaya Materai</td>
															<td class="col-sm-3 text-right kanan">-</td>
															<td class="col-sm-3 text-right kanan">
																<input type="hidden" class="total_pembelian_sapronak" value="<?php echo $total_pembelian_sapronak; ?>">
																<span class="biaya_materai" data-val="<?php echo $data['biaya_materai']; ?>"><?php echo angkaRibuan($data['biaya_materai']); ?></span>
															</td>
														</tr>
														<tr class="<?php echo $hide_internal; ?>">
															<td class="col-sm-6 kiri kanan">CN</td>
															<td class="col-sm-3 text-right kanan cn" data-val="<?php echo $_cn; ?>">
																<?php if ( !empty($id) ): ?>
																	<?php if ( $_cn > 0 ): ?>
							            								<?php echo angkaDecimal($_cn); ?>
							            							<?php else: ?>
							            								<?php echo '-'; ?>
							            							<?php endif ?>
							            						<?php else: ?>
							            							<input type="text" class="form-control text-right" data-tipe="decimal" style="height: 20px; padding: 3px 6px;" onblur="rg.setVal(this)">
							            						<?php endif ?>
															</td>
															<td class="col-sm-3 text-right kanan">-</td>
														</tr>
														<tr>
															<td class="col-sm-6 kiri kanan">Biaya Operasional</td>
															<td class="col-sm-3 text-right kanan">-</td>
															<td class="col-sm-3 text-right kanan biaya_opr" data-val="<?php echo $biaya_opr; ?>">
																<?php echo angkaRibuan($biaya_opr); ?>
																<!-- <?php if ( !empty($id) ): ?>
																	<?php if ( $biaya_opr > 0 ): ?>
							            								<?php echo angkaRibuan($biaya_opr); ?>
							            							<?php else: ?>
							            								<?php echo '-'; ?>
							            							<?php endif ?>
							            						<?php else: ?>
							            							<?php if ( empty($hide_internal) ): ?>
							            								<input type="text" class="form-control text-right" data-tipe="decimal" style="height: 20px; padding: 3px 6px;" onblur="rg.setVal(this)">
							            							<?php else: ?>
							            								<?php if ( $biaya_opr > 0 ): ?>
								            								<?php echo angkaRibuan($biaya_opr); ?>
								            							<?php else: ?>
								            								<?php echo '-'; ?>
								            							<?php endif ?>
							            							<?php endif ?>
							            						<?php endif ?> -->
															</td>
														</tr>
														<tr class="bottom">
															<td class="col-sm-6 kiri kanan text-right" style="border-top: 1px solid black;"><b>TOTAL</b></td>
															<td class="col-sm-3 text-right kanan total_pemasukan" style="border-top: 1px solid black;" data-val="<?php echo $total_pemasukan; ?>"><b><?php echo angkaRibuan($total_pemasukan); ?></b></td>
															<td class="col-sm-3 text-right kanan total_pengeluaran" style="border-top: 1px solid black;" data-val="<?php echo $total_pengeluaran; ?>"><b><?php echo angkaRibuan($total_pengeluaran); ?></b></td>
														</tr>
													</tbody>
												</table>
											</small>
										</fieldset>
									</div>
									<div class="col-sm-6" style="padding-right: 0px;">
										<fieldset>
											<legend>Performance Peternak</legend>
											<small>
												<table class="table table-nobordered" style="margin-bottom: 0px;">
													<tbody>
														<tr class="top">
															<td class="col-sm-8 kiri kanan">Jumlah Panen (Ekor)</td>
															<td class="col-sm-4 kanan text-right jml_ekor_panen" data-val="<?php echo $total_ekor; ?>"><?php echo angkaRibuan($total_ekor); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">Berat Badan (Kg)</td>
															<td class="col-sm-4 kanan text-right jml_panen_kg" data-val="<?php echo $total_tonase; ?>"><?php echo angkaDecimal($total_tonase); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">BB Rata-Rata / Ekor (Kg)</td>
															<td class="col-sm-4 kanan text-right bb_panen" data-val="<?php echo $bb; ?>"><?php echo angkaDecimal($bb); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">FCR</td>
															<td class="col-sm-4 kanan text-right fcr" data-val="<?php echo $fcr; ?>"><?php echo angkaDecimal($fcr); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">Deplesi</td>
															<td class="col-sm-4 kanan text-right deplesi" data-val="<?php echo $deplesi; ?>"><?php echo angkaDecimal($deplesi); ?></td>
														</tr>
														<tr>
															<td class="col-sm-8 kiri kanan">Rata-Rata Umur</td>
															<td class="col-sm-4 kanan text-right rata_umur" data-val="<?php echo $rata_umur_panen; ?>"><?php echo angkaDecimal($rata_umur_panen); ?></td>
														</tr>
														<tr class="bottom">
															<td class="col-sm-8 kiri kanan"><b>IP</b></td>
															<td class="col-sm-4 kanan text-right ip" data-val="<?php echo $ip; ?>"><b><?php echo angkaDecimal($ip); ?></b></td>
														</tr>
													</tbody>
												</table>
											</small>
										</fieldset>
									</div>
								</div>
							</form>
							<form class="form-horizontal" role="form">
				            	<div class="form-group">
						            <div class="col-sm-12" style="padding-left: 0px; padding-right: 0px;">
						            	<div class="col-sm-6" style="padding-left: 0px;">
							            	<table class="table table-nobordered" style="margin-bottom: 0px;">
							            		<tbody>
							            			<tr>
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;"><b>Laba/Rugi Inti</b></td>
							            				<?php $pendapatan_peternak = $total_pemasukan - $total_pengeluaran; ?>
							            				<td class="col-sm-4 text-right pendapatan_peternak" data-val="<?php echo $pendapatan_peternak; ?>" style="padding-bottom: 0px;"><b><?php echo ($pendapatan_peternak > 0) ? angkaRibuan($pendapatan_peternak) : '('.angkaRibuan(abs($pendapatan_peternak)).')'; ?></b></td>
							            			</tr>
							            			<tr>
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;"><b>Harga Rata Ayam</b></td>
							            				<?php $harga_rata_ayam = ($total_nilai_pasar > 0 && $total_tonase > 0) ? $total_nilai_pasar / $total_tonase : 0; ?>
							            				<td class="col-sm-4 text-right" style="padding-bottom: 0px;"><b><?php echo ($harga_rata_ayam); ?></b></td>
							            			</tr>
							            			<tr>
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;"><b>Modal Inti</b></td>
							            				<?php $modal_inti = ($total_pengeluaran > 0 && $total_tonase > 0) ? $total_pengeluaran / $total_tonase : 0; ?>
							            				<td class="col-sm-4 text-right" style="padding-bottom: 0px;"><b><?php echo ($modal_inti); ?></b></td>
							            			</tr>
							            			<tr>
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;"><b>Modal Inti Sebenarnya</b></td>
							            				<?php $modal_inti_sebenarnya = ($total_pengeluaran > 0 && $total_tonase > 0) ? ($total_pengeluaran - $total_nilai_insentif) / $total_tonase : 0; ?>
							            				<td class="col-sm-4 text-right" style="padding-bottom: 0px;"><b><?php echo angkaRibuan($modal_inti_sebenarnya); ?></b></td>
							            			</tr>
							            			<?php $nilai_potongan_pajak = ($data['potongan_pajak'] > 0) ? ($total_pemasukan - $total_pengeluaran) * ($data['potongan_pajak']/100) : 0; ?>
							            			<tr class="hide">
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;">Potongan Pajak <span class="prs_pajak" data-val="<?php echo $data['potongan_pajak']; ?>"><?php echo angkaDecimal($data['potongan_pajak']); ?>%</span> (PPh Pasal 23)</td>
							            				<td class="col-sm-4 text-right nilai_potongan_pajak" data-val="<?php echo $nilai_potongan_pajak; ?>" style="padding-bottom: 0px;"><?php echo angkaRibuan( $nilai_potongan_pajak ); ?></td>
							            			</tr>
							            			<tr class="hide">
							            				<td class="col-sm-8 text-left" style="padding-bottom: 0px;"><b>Pendapatan Peternak Setelah Kena Pajak</b></td>
							            				<td class="col-sm-4 text-right pendapatan_peternak_setelah_pajak" data-val="<?php echo (($total_pemasukan - $total_pengeluaran) - $nilai_potongan_pajak); ?>" style="padding-bottom: 0px;"><b><?php echo angkaRibuan( ($total_pemasukan - $total_pengeluaran) - $nilai_potongan_pajak ); ?></b></td>
							            			</tr>
							            		</tbody>
							            	</table>
						            	</div>
						            	<?php if ( !empty($id) ): ?>
							            	<div class="col-sm-6 no-padding">
							            		<div class="col-sm-12" style="padding: 10px 0px 0px 0px;">
								            		<button type="button" class="btn btn-default pull-right" onclick="rg.export_excel_inti(this)" data-id="<?php echo exEncrypt($id); ?>"><i class="fa fa-file-excel-o"></i> Export Excel Inti</button>
							            		</div>
							            	</div>
						            	<?php endif ?>
						            	<!-- <div class="col-sm-6 no-padding">
							            	<button type="button" class="btn btn-default pull-right" onclick="rg.export_excel_inti(this)" data-id="<?php echo exEncrypt($id); ?>"><i class="fa fa-print"></i> Export</button>
						            	</div> -->
						            </div>
						    	</div>
						    </form>
						</div>
		        	</div>
		        </div>
		    </fieldset>
        </div>
    </div>
</div>