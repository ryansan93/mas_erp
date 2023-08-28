<?php if ( count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr>
			<td><?php echo tglIndonesia($v_data['tgl_bayar'], '-', ' '); ?></td>
			<td class="transaksi" data-val="<?php echo $v_data['transaksi']; ?>"><?php echo $v_data['transaksi']; ?></td>
			<td class="no_bayar" data-val="<?php echo $v_data['no_bayar']; ?>"><?php echo $v_data['no_bayar']; ?></td>
			<td><?php echo $v_data['periode']; ?></td>
			<td><?php echo $v_data['nama_penerima']; ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['tagihan']); ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['bayar']); ?></td>
			<td class="text-right tagihan" data-val="<?php echo $v_data['jumlah']; ?>"><?php echo angkaDecimal($v_data['jumlah']); ?></td>
			<td class="text-center">
				<?php if ( $v_data['jumlah'] > 0 ): ?>
					<input type="checkbox" class="cursor-p check" target="check">
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="7">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>