var jenis_pengiriman = null;
var pp = {
	start_up: function() {
		pp.setting_up();
	}, // end - start_up

	setting_up: function(){
		$('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            $(this).priceFormat(Config[$(this).data('tipe')]);
        });

        $('.date').datetimepicker({
			locale: 'id',
            format: 'DD MMM Y'
		});

		$('[name=bulan_docin]').datetimepicker({
			locale: 'id',
            format: 'MMM Y'
		});

		$.map( $('.date'), function(ipt) {
			var minDate = new Date();
            var tgl = $(ipt).find('input').data('tgl');
            if ( !empty(tgl) ) {
            	minDate = new Date(tgl);
            }
            
            $(ipt).data("DateTimePicker").date(new Date(tgl));
        });

        $.map( $('[name=bulan_docin]'), function(ipt) {
            var tgl = $(ipt).data('tgl');
            if ( !empty(tgl) ) {
                $(ipt).data('DateTimePicker').date(new Date(tgl));
            }
        });

        $('select.peternak_asal').select2();
        $('select.gudang_asal').select2();
        $('select.peternak').select2();
        $('select.gudang').select2();
        $('select.ekspedisi').select2();
	}, // end - setting_up

	addRowChild: function(elm) {
        let row = $(elm).closest('tr');
        
		var err = 0;
		$.map( $(row).find('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu sebelum menambah baris baru.');
		} else {
			let newRow = row.clone();
	        newRow.find('input, select').val('');
	        row.find('.btn-ctrl').hide();
	        row.after(newRow);
	        
	        let tbody = $(row).closest('tbody');
	        if ( $(tbody).find('tr').length > 0 ) {
	        	newRow.find('.btn_del_row_2x').removeClass('hide');
	        };

	        App.formatNumber();
		}
    }, // end - addRowChildOrderPakan

    removeRowChild: function(elm) {
        let row = $(elm).closest('tr');
        if ($(row).prev('tr').length > 0) {
            $(row).prev('tr').find('.btn-ctrl').show();
            $(row).remove();
        }else{
            $(row).prev('tr').find('.btn-ctrl').show();
        }
    }, // end - removeRowChild

    changeTabActive: function(elm) {
        var vhref = $(elm).data('href');
        // change tab-menu
        $('.nav-tabs').find('a').removeClass('active');
        $('.nav-tabs').find('a').removeClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

        // change tab-content
        $('.tab-pane').removeClass('show');
        $('.tab-pane').removeClass('active');
        $('div#'+vhref).addClass('show');
        $('div#'+vhref).addClass('active');

        if ( vhref == 'pengiriman' ) {
            var v_id = $(elm).attr('data-id');
            var v_resubmit = $(elm).attr('data-resubmit');

            pp.load_form(v_id, v_resubmit);
        };
    }, // end - changeTabActive

    load_form: function(v_id = null, v_resubmit = null) {
        var dcontent = $('div#pengiriman');

        $.ajax({
            url : 'transaksi/PengirimanPakan/load_form',
            data : {
                'id' :  v_id,
                'resubmit': v_resubmit
            },
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ showLoading(); },
            success : function(html){
                $(dcontent).html(html);
                pp.setting_up();

                if ( !empty(v_id) ) {
                	$.map( $(dcontent).find('[name=bulan_docin]'), function(ipt) {
                		pp.get_peternak(ipt);
                	});

                	if ( empty(v_resubmit) ) {
                		hideLoading();
                	}
                } else {
                	hideLoading();
                }
            },
        });
    }, // end - load_form

    get_lists: function() {
    	var div_riwayat = $('div#riwayat');

    	var start_date = $(div_riwayat).find('[name=startDate]').data('DateTimePicker').date();
		var end_date = $(div_riwayat).find('[name=endDate]').data('DateTimePicker').date();
		var kode_unit = $(div_riwayat).find('select.unit').val();

		if ( empty(start_date) || empty(end_date) ) {
			bootbox.alert('Harap lengkapi periode terlebih dahulu.');
		} else {
			var start_date = dateSQL( $(div_riwayat).find('[name=startDate]').data('DateTimePicker').date() );
			var end_date = dateSQL( $(div_riwayat).find('[name=endDate]').data('DateTimePicker').date() );

			var params = {
				'start_date': start_date,
				'end_date': end_date,
				'kode_unit': kode_unit
			};

			$.ajax({
				url: 'transaksi/PengirimanPakan/get_lists',
				data: {'params': params},
				type: 'POST',
				dataType: 'JSON',
				beforeSend: function() {
					showLoading();
				},
				success: function(data) {
					hideLoading();
					if ( data.status == 1 ) {
						$(div_riwayat).find('table.tbl_pengiriman tbody').html( data.content );
					};
				},
		    });
		}
    }, // end - get_lists

    get_op_not_kirim: function(elm) {
    	var div_filter = $(elm).closest('div.filter');
    	var div_action = $(div_filter).closest('div#pengiriman');

    	var unit = $(div_filter).find('.unit').val();
    	var tgl_kirim = $(div_filter).find('div#tgl_kirim_op input').val();

    	if ( empty(unit) || empty(tgl_kirim) ) {
    		bootbox.alert('Harap isi data filter terlebih dahulu.');
    	} else {
    		var params = {
    			'unit': unit,
    			'tgl_kirim': dateSQL( $(div_filter).find('[name=tgl_kirim_op]').data('DateTimePicker').date() )
    		};

    		$.ajax({
				url: 'transaksi/PengirimanPakan/get_op_not_kirim',
				data: {
					'params': params
				},
				type: 'POST',
				dataType: 'JSON',
				beforeSend: function() {
					showLoading();
				},
				success: function(data) {
					var id = $(div_action).find('select.no_order').data('id');

					var option = '<option value="">-- Pilih No. Order --</option>';
					if ( data.content.length > 0 ) {
						for (var i = 0; i < data.content.length; i++) {
							var selected = '';
							if ( !empty(id) ) {
								if ( id == data.content[i].id ) {
									selected = 'selected';
								}
							}
							option += '<option value="'+data.content[i].no_order+'" data-supplier="'+data.content[i].d_supplier.nama+'" data-idsupplier="'+data.content[i].d_supplier.nomor+'" '+selected+' >'+data.content[i].no_order+'</option>';
						}
					}

					// $(div_action).find('select.no_order').removeAttr('disabled');
					$(div_action).find('select.no_order').html(option);

					hideLoading();
				},
		    });
    	}
    }, // end - get_op_not_kirim

	cek_jenis: function(elm) {
		var div = $(elm).closest('div.detailed');
		jenis_pengiriman = $(elm).val();

		$(div).find('div.opkp').addClass('hide');
		$(div).find('div.opkp select').removeAttr('data-required');
		$(div).find('div.opkg').addClass('hide');
		$(div).find('div.opkg select').removeAttr('data-required');

		if ( jenis_pengiriman == 'opks' ) {
			$(div).find('input[data-jenis=opks], select[data-jenis=opks]').removeClass('hide');
			$(div).find('input[data-jenis=opks], select[data-jenis=opks]').attr('data-required', 1);
			$(div).find('input[data-jenis=opks], select[data-jenis=opks]').removeAttr('disabled');
			$(div).find('input[data-jenis=non_opks], select[data-jenis=non_opks]').addClass('hide');
			$(div).find('input[data-jenis=non_opks], select[data-jenis=non_opks]').removeAttr('data-required');
			$(div).find('input[data-jenis=non_opks]').attr('readonly', true);
			$(div).find('input.no_sj').attr('readonly', false);
			$(div).find('input.no_sj').attr('data-required', 1);

			$(div).find('div.opks').removeClass('hide');
			$(div).find('div.opks input').attr('data-required', 1);

			$('table.tbl_detail_brg:not(.hide)').find('select.barang').attr('disabled', false);
			$('table.tbl_detail_brg:not(.hide)').find('input.jumlah').attr('disabled', false);
			$('table.tbl_detail_brg:not(.hide)').find('input.kondisi').attr('disabled', false);
			// $(div).find('div.opkp').addClass('hide');
			// $(div).find('div.opkp select').removeAttr('data-required');
			// $(div).find('div.opkg').addClass('hide');
			// $(div).find('div.opkg select').removeAttr('data-required');
			$(div).find('table.opkp').addClass('hide');
			$(div).find('table.non_opkp').removeClass('hide');

			$(div).find('input.ongkos_angkut').val('');
			$(div).find('input.ongkos_angkut').removeAttr('disabled');
		} else {
			$(div).find('input[data-jenis=opks], select[data-jenis=opks]').addClass('hide');
			$(div).find('input[data-jenis=opks], select[data-jenis=opks]').removeAttr('data-required', 1);
			$(div).find('input[data-jenis=non_opks], select[data-jenis=non_opks]').removeClass('hide');
			$(div).find('input[data-jenis=non_opks], select[data-jenis=non_opks]').attr('data-required');
			$(div).find('input.no_sj').attr('readonly', true);
			$(div).find('input.no_sj').removeAttr('data-required');
			// $(div).find('input[data-jenis=non_opks]').removeAttr('readonly');

			$(div).find('div.opks').addClass('hide');
			$(div).find('div.opks input').removeAttr('data-required');
			if ( jenis_pengiriman == 'opkp' ) {
				$(div).find('div.opkp').removeClass('hide');
				$(div).find('div.opkp select').attr('data-required', 1);
				$(div).find('div.opkg').addClass('hide');

				$(div).find('table.opkp').removeClass('hide');
				$(div).find('table.non_opkp').addClass('hide');

				$(div).find('input.ongkos_angkut').val( numeral.formatDec(0) );
				$(div).find('input.ongkos_angkut').attr('disabled', 'disabled');
			} else if ( jenis_pengiriman == 'opkg' ) {
				$(div).find('div.opkp').addClass('hide');
				$(div).find('div.opkg').removeClass('hide');
				$(div).find('div.opkg select').attr('data-required', 1);

				$(div).find('table.opkp').addClass('hide');
				$(div).find('table.non_opkp').removeClass('hide');

				$(div).find('input.ongkos_angkut').val('');
				$(div).find('input.ongkos_angkut').removeAttr('disabled');

				pp.get_list_table(null);
			}
		}
	}, // end - cek_jenis

	cek_tujuan: function(elm) {
		var div = $(elm).closest('div.detailed');
		var tujuan = $(elm).val();

		if ( tujuan == 'peternak' ) {
			$(div).find('div.div_peternak').removeClass('hide');
			$(div).find('select.peternak').attr('data-required', 1);
			$(div).find('div.gudang').addClass('hide');
			$(div).find('select.gudang').removeAttr('data-required');
		} else {
			$(div).find('div.div_peternak').addClass('hide');
			$(div).find('select.peternak').removeAttr('data-required');
			$(div).find('div.gudang').removeClass('hide');
			$(div).find('select.gudang').attr('data-required', 1);
		}
	}, // end - cek_tujuan

	get_asal: function(elm) {
		var div = $(elm).closest('div.detailed');
		var asal = $(elm).find('option:selected').data('supplier');
		var id_supplier = $(elm).find('option:selected').data('idsupplier');
		var no_order = $(elm).find('option:selected').text();

		$(div).find('input.asal').val(asal);
		$(div).find('input.asal').attr('data-id', id_supplier);

		pp.get_list_table(no_order);
	}, // end - get_asal

	get_peternak: function(elm) {
		var div = $(elm).closest('div.div_peternak');
		var periode = dateSQL( $(div).find('[name=bulan_docin]').data('DateTimePicker').date() );

		var noreg = $(div).find('select').data('noreg');

		$.ajax({
			url: 'transaksi/PengirimanPakan/get_peternak',
			data: {
				'params': periode
			},
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function() {
				showLoading();
			},
			success: function(data) {
				var option = '<option value="">-- Pilih Peternak --</option>';
				if ( data.status == 1 ) {
					var idx = 1;
					for (var i = 0; i < data.content.length; i++) {
						var selected = '';
						if ( !empty(noreg) ) {
							if ( noreg == data.content[i].noreg ) {
								selected = 'selected';
							}
						}
						option += '<option value="'+data.content[i].noreg+'" '+selected+'>'+data.content[i].kode_unit.toUpperCase()+' | '+data.content[i].tgl_terima+' | '+data.content[i].nama.toUpperCase()+' ('+data.content[i].noreg.toUpperCase()+')</option>';

						idx++;
						if ( idx == data.content.length ) {
							hideLoading();
						}
					}
				} else {
					hideLoading();
				};
				$(div).find('select').html(option);

			},
	    });
	}, // end - get_peternak

	get_list_table: function(no_order = null) {
		var table = $('table');

		var table = $('table.tbl_detail_brg:not(.hide)');
		var tbody = $(table).find('tbody');

		$.ajax({
			url: 'transaksi/PengirimanPakan/get_list_table',
			data: {
				'jenis_pengiriman': jenis_pengiriman,
				'no_order': no_order
			},
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function() {
				App.showLoaderInContent(tbody);
			},
			success: function(data) {
				if ( data.status == 1 ) {
					App.hideLoaderInContent(tbody, data.content);

					if ( jenis_pengiriman == 'opkg' ) {
						$(tbody).find('select.barang').attr('disabled', true);
						$(tbody).find('input.jumlah').attr('disabled', true);
						$(tbody).find('input.kondisi').attr('disabled', true);
					}

					pp.setting_up();
				};
			},
	    });
	}, // end - get_list_table

	save_kirim_pakan: function() {
		var div_pengiriman = $('div#pengiriman');

		var err = 0;
		$.map( $(div_pengiriman).find('[data-required=1]:not(.hide)'), function(ipt) {
			var table = $(ipt).closest('table');

			if ( $(table).length > 0 ) {
				if ( !$(table).hasClass('hide') ) {
					if ( empty($(ipt).val()) ) {
						$(ipt).parent().addClass('has-error');
						err++;
					} else {
						$(ipt).parent().removeClass('has-error');
					}
				}
			} else {
				if ( empty($(ipt).val()) ) {
					$(ipt).parent().addClass('has-error');
					err++;
				} else {
					$(ipt).parent().removeClass('has-error');
				}
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if (result) {
					var tgl_kirim = dateSQL( $('[name=tgl_kirim]').data('DateTimePicker').date() );
					var jenis_kirim = $('.jenis_kirim').val();
					var no_order = null;
					var asal = null;
					if ( jenis_kirim == 'opks' ) {
						no_order = $('select.no_order').val();
						asal = $('input.asal').data('id');
					} else {
						no_order = $('input.no_order').val();
						if ( jenis_kirim == 'opkp' ) {
							asal = $('select.peternak_asal').select2('val');
						} else if ( jenis_kirim == 'opkg' ) {
							asal = $('select.gudang_asal').select2('val');
						}
					}
					var jenis_tujuan = $('select.tujuan').val();
					var tujuan = null;
					if ( jenis_tujuan == 'peternak' ) {
						tujuan = $('select.peternak').select2('val')
					} else {
						tujuan = $('select.gudang').select2('val')
					}
					var ekspedisi_id = empty($('.ekspedisi').select2('val')) ? $('.ekspedisi').select2('val') : $('.ekspedisi').val()
					var ekspedisi = $('.ekspedisi option:selected').attr('data-nama');
					var nopol = $('input.no_pol').val();
					var sopir = $('input.sopir').val();
					var no_sj = $('input.no_sj').val();
					var ongkos_angkut = numeral.unformat($('input.ongkos_angkut').val());

					var detail = $.map( $('table.tbl_detail_brg:not(.hide) tbody tr'), function(tr) {
						var _data = {
							'no_sj_asal': ($(tr).find('select.no_sj_asal').length > 0) ? $(tr).find('select.no_sj_asal').val() : null,
							'barang': $(tr).find('select.barang').val(),
							'jumlah': numeral.unformat( $(tr).find('input.jumlah').val() ),
							'kondisi': $(tr).find('input.kondisi').val()
						}

						return _data;
					});

					var data = {
						'tgl_kirim': tgl_kirim,
						'jenis_kirim': jenis_kirim,
						'no_order': no_order,
						'asal': asal,
						'jenis_tujuan': jenis_tujuan,
						'tujuan': tujuan,
						'ekspedisi_id': ekspedisi_id,
						'ekspedisi': ekspedisi,
						'nopol': nopol,
						'sopir': sopir,
						'no_sj': no_sj,
						'ongkos_angkut': ongkos_angkut,
						'detail': detail
					};

					pp.exec_save_kirim_pakan(data);
				}
			});
		}
	}, // end - save_kirim_pakan

	exec_save_kirim_pakan: function(params = null) {
		var table = $('table');
		var tbody = $(table).find('tbody');

		$.ajax({
			url: 'transaksi/PengirimanPakan/save',
			data: {
				'params': params
			},
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function() {
				showLoading();
			},
			success: function(data) {
				hideLoading();
				if ( data.status == 1 ) {
					bootbox.alert(data.message, function() {
						var div_riwayat = $('div#riwayat');
				    	var start_date = $(div_riwayat).find('[name=startDate]').data('DateTimePicker').date();
						var end_date = $(div_riwayat).find('[name=endDate]').data('DateTimePicker').date();
						if ( !empty(start_date) && !empty(end_date) ) {
							// pp.get_lists();
						}

						var btn = '<button data-href="riwayat">';
						pp.changeTabActive(btn);
						pp.load_form();
					});
				} else {
					bootbox.alert(data.message);
				};
			},
	    });
	}, // end - exec_save_kirim_pakan

	edit_kirim_pakan: function(elm) {
		var div_pengiriman = $('div#pengiriman');

		var err = 0;
		$.map( $(div_pengiriman).find('[data-required=1]:not(.hide)'), function(ipt) {
			var table = $(ipt).closest('table');

			if ( $(table).length > 0 ) {
				if ( !$(table).hasClass('hide') ) {
					if ( empty($(ipt).val()) ) {
						$(ipt).parent().addClass('has-error');
						err++;
					} else {
						$(ipt).parent().removeClass('has-error');
					}
				}
			} else {
				if ( empty($(ipt).val()) ) {
					$(ipt).parent().addClass('has-error');
					err++;
				} else {
					$(ipt).parent().removeClass('has-error');
				}
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin mengubah data ?', function(result) {
				if (result) {
					var id = $(elm).data('id');
					var tgl_kirim = dateSQL( $('[name=tgl_kirim]').data('DateTimePicker').date() );
					var jenis_kirim = $('.jenis_kirim').val();
					var no_order = null;
					var asal = null;
					if ( jenis_kirim == 'opks' ) {
						no_order = $('select.no_order').val();
						asal = $('input.asal').data('id');
					} else {
						no_order = $('input.no_order').val();
						if ( jenis_kirim == 'opkp' ) {
							asal = $('select.peternak_asal').select2('val');
						} else if ( jenis_kirim == 'opkg' ) {
							asal = $('select.gudang_asal').select2('val');
						}
					}
					var jenis_tujuan = $('select.tujuan').val();
					var tujuan = null;
					if ( jenis_tujuan == 'peternak' ) {
						tujuan = $('select.peternak').select2('val')
					} else {
						tujuan = $('select.gudang').select2('val')
					}
					var ekspedisi_id = empty($('.ekspedisi').select2('val')) ? $('.ekspedisi').select2('val') : $('.ekspedisi').val()
					var ekspedisi = $('.ekspedisi option:selected').attr('data-nama');
					var nopol = $('input.no_pol').val();
					var sopir = $('input.sopir').val();
					var no_sj = $('input.no_sj').val();
					var ongkos_angkut = numeral.unformat($('input.ongkos_angkut').val());

					var detail = $.map( $('table.tbl_detail_brg:not(.hide) tbody tr'), function(tr) {
						var _data = {
							'no_sj_asal': ($(tr).find('select.no_sj_asal').length > 0) ? $(tr).find('select.no_sj_asal').val() : null,
							'barang': $(tr).find('select.barang').val(),
							'jumlah': numeral.unformat( $(tr).find('input.jumlah').val() ),
							'kondisi': $(tr).find('input.kondisi').val()
						}

						return _data;
					});

					var data = {
						'id': id,
						'tgl_kirim': tgl_kirim,
						'jenis_kirim': jenis_kirim,
						'no_order': no_order,
						'asal': asal,
						'jenis_tujuan': jenis_tujuan,
						'tujuan': tujuan,
						'ekspedisi_id': ekspedisi_id,
						'ekspedisi': ekspedisi,
						'nopol': nopol,
						'sopir': sopir,
						'no_sj': no_sj,
						'ongkos_angkut': ongkos_angkut,
						'detail': detail
					};

					pp.exec_edit_kirim_pakan(data);
				}
			});
		}
	}, // end - edit_kirim_pakan

	exec_edit_kirim_pakan: function(params = null) {
		var table = $('table');
		var tbody = $(table).find('tbody');

		$.ajax({
			url: 'transaksi/PengirimanPakan/edit',
			data: {
				'params': params
			},
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function() {
				showLoading();
			},
			success: function(data) {
				hideLoading();
				if ( data.status == 1 ) {
					bootbox.alert(data.message, function() {
						// pp.get_lists();

						var btn = '<button data-href="riwayat">';
						pp.changeTabActive(btn);
						pp.load_form();
					});
				} else {
					bootbox.alert(data.message);
				};
			},
	    });
	}, // end - exec_edit_kirim_pakan

	delete: function(elm) {
		var id = $(elm).data('id');

		var params = {'id': id};

		bootbox.confirm('Apakah anda yakin ingin menghapus data ?', function(result) {
			if ( result ) {
				$.ajax({
					url: 'transaksi/PengirimanPakan/delete',
					data: {
						'params': params
					},
					type: 'POST',
					dataType: 'JSON',
					beforeSend: function() {
						showLoading();
					},
					success: function(data) {
						hideLoading();
						if ( data.status == 1 ) {
							bootbox.alert(data.message, function() {
								pp.get_lists();
								pp.load_form();
							});
						} else {
							bootbox.alert(data.message);
						};
					},
			    });
			}
		});
	}, // end - delete

	cek_gudang: function(elm) {
		var gudang = $(elm).val();

		if ( !empty(gudang) ) {
			$('table.tbl_detail_brg:not(.hide)').find('select.barang').attr('disabled', false);
			$('table.tbl_detail_brg:not(.hide)').find('input.jumlah').attr('disabled', false);
			$('table.tbl_detail_brg:not(.hide)').find('input.kondisi').attr('disabled', false);
		} else {
			$('table.tbl_detail_brg:not(.hide)').find('select.barang').attr('disabled', true);
			$('table.tbl_detail_brg:not(.hide)').find('input.jumlah').attr('disabled', true);
			$('table.tbl_detail_brg:not(.hide)').find('input.kondisi').attr('disabled', true);
		}
	}, // end - cek_gudang

	cek_stok_gudang: function(elm) {
		var jenis_kirim = $('select.jenis_kirim').val();

		if ( jenis_kirim == 'opkg' ) {
			var tr = $(elm).closest('tr');
			var select_item = $(tr).find('select.barang');

			var jml = numeral.unformat($(elm).val());
			var item = $(select_item).val();
			var gudang = $('select.gudang_asal').val();

			var params = {
				'jml': jml,
				'item': item,
				'gudang': gudang,
			};

			$.ajax({
				url: 'transaksi/PengirimanPakan/cek_stok_gudang',
				data: {
					'params': params
				},
				type: 'POST',
				dataType: 'JSON',
				beforeSend: function() {},
				success: function(data) {
					if ( data.status == 1 ) {
						if ( data.status_stok == 0 ) {
							bootbox.alert( data.message, function() {
								$(elm).val(0);
							});
						}
					} else {
						bootbox.alert( data.message );
					}
				},
		    });
		}
	}, // end - cek_stok_gudang

	listActivity: function(elm) {
		let tr = $(elm).closest('tr');

        let params = {
            'id' : $(elm).data('id'),
            'no_order' : $(tr).find('td.no_order').text(),
            'tgl_kirim' : $(tr).find('td.tgl_kirim').text(),
            'asal' : $(tr).find('td.asal').text(),
            'tujuan' : $(tr).find('td.tujuan').text(),
            'nopol' : $(tr).find('td.nopol').text(),
        }

        $.get('transaksi/PengirimanPakan/listActivity',{
                'params': params
            },function(data){
            var _options = {
                className : 'veryWidth',
                message : data,
                size : 'large',
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                var modal_dialog = $(this).find('.modal-dialog');
                $(modal_dialog).css({'max-width' : '80%'});
                $(modal_dialog).css({'width' : '80%'});

                var modal_header = $(this).find('.modal-header');
                $(modal_header).css({'padding-top' : '0px'});
            });
        },'html');
	}, // end - listActivity

	getSjAsal: function(elm) {
		var noreg = $(elm).val();

		var option_no_sj = "<option value=''>No. SJ Awal</option>";
		if ( !empty(noreg) ) {
			var params = {
				'noreg': noreg
			};

			$.ajax({
				url: 'transaksi/PengirimanPakan/getSjAsal',
				data: {
					'params': params
				},
				type: 'POST',
				dataType: 'JSON',
				beforeSend: function() { showLoading(); },
				success: function(data) {
					hideLoading();
					if ( data.status == 1 ) {
						if ( data.content.data.length > 0 ) {
							for (var i = 0; i < data.content.data.length; i++) {
								var _data = data.content.data[i];
								var barang = data.content.data[i].barang;

								option_no_sj += "<option value='"+_data.no_sj+"' data-barang='"+JSON.stringify(barang)+"'>"+_data.text_tgl+" | "+_data.no_sj+"</option>";
							}
						}
					} else {
						bootbox.alert( data.message );
					}
					$('.no_sj_asal').html( option_no_sj );
				},
		    });
		} else {
			$('.no_sj_asal').html( option_no_sj );
		}
	}, // end - get_sj_asal

	getBarang: function(elm) {
		var tr = $(elm).closest('tr');
		var no_sj = $(elm).val();

		var option_barang = '<option value="">Pilih Barang</option>';

		if ( !empty(no_sj) ) {
			var barang = JSON.parse( $(elm).find('option:selected').attr('data-barang') );

			if ( barang.length > 0 ) {
				for (var j = 0; j < barang.length; j++) {
					var _barang = barang[j];

					option_barang += '<option value="'+_barang.kode+'" data-jmlmax="'+_barang.jumlah+'">'+_barang.nama.toUpperCase()+' ('+numeral.formatInt(_barang.jumlah)+')</option>';
				}
			}
		}
		$(tr).find('.barang').html( option_barang );
	}, // end - getBarang

	pilihBarang: function(elm) {
		var tr = $(elm).closest('tr');
		var kode_barang = $(elm).val();

		if ( !empty(kode_barang) ) {
			$(tr).find('input.jumlah').removeAttr('disabled');
		} else {
			$(tr).find('input.jumlah').attr('disabled', 'disabled');
		}
	}, // end - pilihBarang

	cekJmlPindah: function(elm) {
		var tr = $(elm).closest('tr');

		var jml_pindah = numeral.unformat($(elm).val());
		var jml_max = $(tr).find('.barang option:selected').attr('data-jmlmax');

		if ( jml_pindah > jml_max ) {
			bootbox.alert('Jumlah yang anda masukkan melebihi jumlah terima.', function() {
				$(elm).val( 0 );
			});
		}
	}, // end - cekJmlPindah
};

pp.start_up()