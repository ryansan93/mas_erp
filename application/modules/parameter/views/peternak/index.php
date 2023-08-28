<div class="row content-panel detailed" id="index">
	<!-- <h4 class="mb">Master Peternak</h4> -->
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="panel-heading">
				<ul class="nav nav-tabs nav-justified">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#history" data-tab="history">Riwayat Peternak</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#action" data-tab="action">Peternak</a>
					</li>
				</ul>
			</div>

			<div class="panel-body">
				<div class="tab-content">
					<div id="history" class="tab-pane fade show active">
						<div class="col-lg-8 no-padding">
							<div class="col-lg-2" style="padding: 0px 5px 0px 0px;">
								<select class="form-control" id="search-by-pagination">
									<option value="">Search By</option>
									<option value="nama">Nama</option>
									<option value="unit">Unit</option>
								</select>
							</div>
							<div class="col-lg-6 search left-inner-addon" style="padding: 0px 5px 0px 0px;">
								<i class="glyphicon glyphicon-search"></i><input class="form-control" id="search-val-pagination" type="search" placeholder="Search">
							</div>
							<div class="col-lg-2 no-padding">
								<button type="button" id="search-pagination" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="col-lg-4 action no-padding">
							<?php if ( $akses['a_submit'] == 1 ) { ?>
								<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="ADD" onclick="ptk.changeTabActive(this)" style="margin-left: 10px;"> 
									<i class="fa fa-plus" aria-hidden="true"></i> ADD
								</button>
							<?php } else if ( $akses['a_approve'] == 1 ) { ?>
								<button id="btn-approve" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="APPROVE" onclick="ptk.ack_reject(this)" data-action="approve"> 
									<i class="fa fa-check" aria-hidden="true"></i> APPROVE
								</button>
							<?php } ?>
							<button id="btn-export" type="button" class="btn btn-default cursor-p pull-right" title="EXPORT" onclick="ptk.form_export_excel(this)"> 
								<i class="fa fa-print" aria-hidden="true"></i> EXPORT EXCEL
							</button>
						</div>

						<div class="col-lg-12 no-padding">
							<table class="table table-bordered table-hover tbl_peternak" id="dataTable" width="100%" cellspacing="0">
								<thead>
									<tr>
										<?php if ($akses['a_approve'] == 1 ): ?>
											<th>
												<div class="checkbox checkbox-primary text-center">
													<input type="checkbox" class="styled styled-primary" id="markAll" onclick="ptk.set_mark_all(this)">
												</div>
											</th>
										<?php endif; ?>
										<th class="col-lg-2" style="max-width: 10.5%;">Nomor</th>
										<th class="col-lg-1">KTP</th>
										<th class="col-lg-2">Nama Mitra</th>
										<th class="col-lg-1">Unit</th>
										<th class="col-lg-3">Alamat</th>
										<th class="col-lg-1">Status</th>
										<th class="col-lg-2">Keterangan</th>
									</tr>
								</thead>
								<tbody>
									<?php if ( !empty($list_mitra) ): ?>
										<?php foreach ($list_mitra as $mitra): ?>
										<?php endforeach ?>
									<?php else: ?>
										<tr>
											<td class="text-center" colspan="7">Data tidak ditemukan.</td>
										</tr>
									<?php endif ?>
								</tbody>
							</table>
						</div>

						<div class="col-lg-12 no-padding">
							<div id="pagination-ry"></div>
						</div>
					</div>

					<div id="action" class="tab-pane fade">
						<?php if ( $akses['a_submit'] == 1 ): ?>
							<?php echo $add_form; ?>
						<?php else: ?>
							<h3>Data Kosong.</h3>
						<?php endif ?>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>