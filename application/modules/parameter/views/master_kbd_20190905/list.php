<!-- UNTUK ISI DARI LIST MASTER KBD -->
<?php foreach ($list as $key => $val) { ?>
	<?php 
		$resubmit = null;
		if ( $val['g_status'] == 4 ) {
			$resubmit = $val['id'];
		}
	?>

	<?php 
		$red = null;
		if ( $akses['a_ack'] == 1 ){
			$status = getStatus('submit');
			if ( $val['g_status'] == $status ) {
				$red = 'red';
			}
		} else if ( $akses['a_approve'] == 1 ){
			$status = getStatus('ack');
			if ( $val['g_status'] == 2 ) {
				$red = 'red';
			}
		} else {

		}
	?>

	<tr class="<?php echo $red; ?>">
		<td><?php echo tglIndonesia($val['mulai'], '-', ' '); ?></td>
		<td><?php echo $val['nomor']; ?></td>
		<td><?php echo strtoupper($val['pola_kerjasama']['item']); ?></td>
		<td><?php echo $val['item_pola']; ?></td>
		<td>
			<div class="col-sm-10 no-padding">
				<?php 
					if ( isset($val['logs'][ count($val['logs']) - 1 ]) ) {
						$last_log = $val['logs'][ count($val['logs']) - 1 ];
						$keterangan = $last_log['deskripsi'] . ' pada ' . dateTimeFormat( $last_log['waktu'] );
					} else {
						$keterangan = '-';
					}

					echo $keterangan;
				?>
			</div>
			<div class="col-sm-1 no-padding">
				<?php if ( $akses['a_edit'] == 1 ){ ?>
					<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="EDIT" onclick="kbd.changeTabActive(this)" data-id="<?php echo $val['id']; ?>" data-resubmit="<?php echo 'EDIT'; ?>" data-mulai="<?php echo $val['mulai']; ?>"> 
						<i class="fa fa-edit" aria-hidden="true"></i>
					</button>
				<?php } ?>
			</div>
			<div class="col-sm-1 no-padding">
				<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="LIHAT" onclick="kbd.changeTabActive(this)" data-id="<?php echo $val['id']; ?>" data-resubmit="<?php echo $resubmit; ?>" data-mulai="<?php echo $val['mulai']; ?>"> 
					<i class="fa fa-file" aria-hidden="true"></i>
				</button>
			</div>
		</td>
   </tr>
<?php } ?>