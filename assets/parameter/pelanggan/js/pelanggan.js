var plg = {
	start_up : function	() {
		$('button#search-pagination').on('click', function() {
			plg.set_pagination();
		});
		
		plg.setBindSHA1();
		plg.set_pagination();

		$('[data-tipe=phone]').mask("9999 9999 9??999");
		$('[data-tipe=rt]').mask("999");
		$('[data-tipe=rw]').mask("999");
		$('[name=ktp_plg]').mask("9999999999999999");
		$('[name=npwp_plg]').mask("99.999.999.9-999.999");

		$('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            $(this).priceFormat(Config[$(this).data('tipe')]);
        });
	}, // end - start_up

	set_pagination : function(){
		var search_by = $('#search-by-pagination').val();
		var search_val = $('#search-val-pagination').val();

		$.ajax({
			url : 'parameter/Pelanggan/amount_of_data',
			data : {'search_by': search_by, 'search_val': search_val},
			dataType : 'JSON',
			type : 'POST',
			beforeSend : function(){},
			success : function(data){
				pagination.set_pagination( data.content.jml_row, data.content.jml_page, data.content.list, plg.getLists );
			}
		});
	}, // end - set_pagination

	getLists : function(elm){
		var list_nomor = $(elm).data('list_id_page');
        $.ajax({
            url : 'parameter/Pelanggan/list_plg',
            data : {'params' : list_nomor},
            dataType : 'HTML',
            type : 'GET',
            beforeSend : function(){ showLoading(); },
            success : function(data){
                $('table.tbl_plg tbody').html(data);

                hideLoading();
            }
        });
    }, // end - getLists

	setBindSHA1 : function(){
		$('input:file').off('change.sha1');
		$('input:file').on('change.sha1',function(){
			var elm = $(this);
			var file = elm.get(0).files[0];
			elm.attr('data-sha1', '');
			sha1_file(file).then(function (sha1) {
				elm.attr('data-sha1', sha1);
			});
		});
	}, // end - setBindSHA1

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

        if ( vhref == 'action' ) {
            var v_id = $(elm).attr('data-id');
            var tgl_mulai = $(elm).attr('data-mulai');
            var resubmit = $(elm).attr('data-resubmit');

            plg.load_form(v_id, tgl_mulai, resubmit);
        };
    }, // end - changeTabActive

    load_form: function(v_id = null, tgl_mulai = null, resubmit = null) {
        var div_action = $('div#action');

        $.ajax({
            url : 'parameter/Pelanggan/load_form',
            data : {
                'id' :  v_id,
                'resubmit' : resubmit
            },
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ showLoading(); },
            success : function(html){
                $(div_action).html(html);

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $('[data-tipe=phone]').mask("9999 9999 9??999");
				$('[data-tipe=rt]').mask("999");
				$('[data-tipe=rw]').mask("999");
				$('[name=ktp_plg]').mask("9999999999999999");
				var npwp = $('[name=npwp_plg]').val();
				if ( !empty(v_id) && empty(resubmit) ) {
					if ( !empty(npwp.trim()) ) {
						$('[name=npwp_plg]').mask("99.999.999.9-999.999");
					} else {
						$('[name=npwp_plg]').val("-");
					}
				} else {
					$('[name=npwp_plg]').mask("99.999.999.9-999.999");
				}

                plg.setBindSHA1();

                if ( !empty(resubmit) ) {
                	plg.load_kab_plg();
                };

                hideLoading();
            },
        });
    }, // end - load_form

    load_kab_plg : function() {
    	var select_prov_plg = $('select[name=propinsi_plg]');
    	var tipe_lok_plg = $('select[name=tipe_lokasi]');
    	var tipe_lok_usaha = $('select[name=tipe_lokasi_usaha]');

    	plg.getListLokasi_Update(tipe_lok_plg, '#alamat_pelanggan', 'kab', '');
    	plg.getListLokasi_UpdateUsaha(tipe_lok_usaha, '#alamat_usaha_pelanggan', 'kab', '_usaha');
    }, // end - load_tipe_lokasi

	list_load : function(elm) {
		$.ajax({
			url : 'parameter/Pelanggan/list_pelanggan',
			dataType : 'HTML',
			beforeSend : function(){
				showLoading();
			},
			success : function(data){
				hideLoading();
				$('#table-list-PLG tbody').html(data);
			}
		});
	}, // end - list_load_do

	getListLokasi : function(elm, close = '', req = '', tipe = ''){
		var _form = $(elm).closest(close);
		var induk = '';
		var jenis = '';

		// reset pilihan kabupaten
		var eSelect = null;
		var optDefault = '';
		if (req == 'kab') {
			eSelect = _form.find('select[name=kabupaten'+tipe+'_plg]'); // element kabupaten
			optDefault = _form.find('select[name=tipe_lokasi'+tipe+'] option:selected').text().toLowerCase();

			induk = _form.find('select[name=propinsi'+tipe+'_plg]').val();
			jenis = _form.find('select[name=tipe_lokasi'+tipe+']').val();

			_form.find('select[name=kecamatan'+tipe+'_plg] option').remove();

		} else if (req = 'kec') {
			eSelect = _form.find('select[name=kecamatan'+tipe+'_plg]');
			optDefault = 'kecamatan';

			induk = _form.find('select[name=kabupaten'+tipe+'_plg]').val();
			jenis = 'KC';
		}

		eSelect.find('option').remove();
		eSelect.append('<option value="" hidden>Pilih '+  optDefault +'</option>')
		if (! empty(jenis) && ! empty(induk)) {
			$.ajax({
				url : 'parameter/Pelanggan/getLokasiJson',
				data : {'jenis' : jenis, 'induk' : induk},
				dataType : 'JSON',
				type : 'GET',
				success : function(data){
					for (loc of data.content) {
						eSelect.append('<option value="'+loc.id+'">'+ loc.nama +'</option>')
					}
				}
			});
		}

	}, // end - onChangeProvinsi

	getListLokasi_Update : function(elm, close = '', req = '', tipe = ''){
		var _form = $(elm).closest(close);
		var induk = '';
		var jenis = '';

		// reset pilihan kabupaten
		var eSelect = null;
		var optDefault = '';

		var id = null;
		if (req == 'kab') {
			eSelect = _form.find('select[name=kabupaten'+tipe+'_plg]'); // element kabupaten

			id = $(eSelect).data('id');

			optDefault = _form.find('select[name=tipe_lokasi'+tipe+'] option:selected').text().toLowerCase();

			induk = _form.find('select[name=propinsi'+tipe+'_plg]').val();
			jenis = _form.find('select[name=tipe_lokasi'+tipe+']').val();

			_form.find('select[name=kecamatan'+tipe+'_plg] option').remove();

		} else if (req = 'kec') {
			eSelect = _form.find('select[name=kecamatan'+tipe+'_plg]');

			id = $(eSelect).data('id');

			optDefault = 'kecamatan';

			induk = _form.find('select[name=kabupaten'+tipe+'_plg]').val();
			jenis = 'KC';
		}

		eSelect.find('option').remove();
		eSelect.append('<option value="" hidden>Pilih '+  optDefault +'</option>')
		if (! empty(jenis) && ! empty(induk)) {
			$.ajax({
				url : 'parameter/Pelanggan/getLokasiJson',
				data : {'jenis' : jenis, 'induk' : induk},
				dataType : 'JSON',
				type : 'GET',
				success : function(data){
					for (loc of data.content) {
						var selected = null;
						if ( id == loc.id ) {
							selected = 'selected';
						};

						eSelect.append('<option value="'+loc.id+'" '+selected+' >'+ loc.nama +'</option>')
					}

					if ( jenis != 'KC' ) {
							var select_kab_plg = $('select[name=kabupaten_plg]');
	    					plg.getListLokasi_Update(select_kab_plg, '#alamat_pelanggan', 'kec', tipe);
					};
				}
			});
		}
	}, // end - onChangeProvinsi

	getListLokasi_UpdateUsaha : function(elm, close = '', req = '', tipe = ''){
		var _form = $(elm).closest(close);
		var induk = '';
		var jenis = '';

		// reset pilihan kabupaten
		var eSelect = null;
		var optDefault = '';

		var id = null;
		if (req == 'kab') {
			eSelect = _form.find('select[name=kabupaten'+tipe+'_plg]'); // element kabupaten

			id = $(eSelect).data('id');

			optDefault = _form.find('select[name=tipe_lokasi'+tipe+'] option:selected').text().toLowerCase();

			induk = _form.find('select[name=propinsi'+tipe+'_plg]').val();
			jenis = _form.find('select[name=tipe_lokasi'+tipe+']').val();

			_form.find('select[name=kecamatan'+tipe+'_plg] option').remove();

		} else if (req = 'kec') {
			eSelect = _form.find('select[name=kecamatan'+tipe+'_plg]');

			id = $(eSelect).data('id');

			optDefault = 'kecamatan';

			induk = _form.find('select[name=kabupaten'+tipe+'_plg]').val();
			jenis = 'KC';
		}

		eSelect.find('option').remove();
		eSelect.append('<option value="" hidden>Pilih '+  optDefault +'</option>')
		if (! empty(jenis) && ! empty(induk)) {
			$.ajax({
				url : 'parameter/Pelanggan/getLokasiJson',
				data : {'jenis' : jenis, 'induk' : induk},
				dataType : 'JSON',
				type : 'GET',
				success : function(data){
					for (loc of data.content) {
						var selected = null;
						if ( id == loc.id.trim() ) {
							selected = 'selected';
						};

						eSelect.append('<option value="'+loc.id+'" '+selected+' >'+ loc.nama +'</option>')
					}

					if ( jenis != 'KC' ) {
							var select_kab_usaha_plg = $('select[name=kabupaten_usaha_plg]');
							plg.getListLokasi_UpdateUsaha(select_kab_usaha_plg, '#alamat_usaha_pelanggan', 'kec', '_usaha')
					};
				}
			});
		}
	}, // end - onChangeProvinsi

	save : function () {
		var error = 0;
		var lbl_errors = [];
		$('[required]').parent().removeClass('has-error');
		$.map($('[required]'), function(elm){
			if( empty( $(elm).val() ) ){
				error++;
				lbl_errors.push( '* ' + $(elm).attr('placeholder') );
				$('[required]').parent().addClass('has-error');
			}else{
				$(elm).parent().removeClass('has-error');
			}
		});

		if (error > 0) {
			bootbox.alert('Data belum lengkap : <br> ' + lbl_errors.join('<br>') );
		} else {
			bootbox.confirm('Apakah anda yakin data mitra akan disimpan?', function(result){
    			if (result) {
    				var div_pelanggan = $('div[name=data-pelanggan]');
    				var rek_pelanggan = $('div#rekening_pelanggan');

        			var formData = new FormData();
        			var lampirans = $.map( $(div_pelanggan).find('input[type=file]'), function(ipt){
			            if (!empty( $(ipt).val() )) {
							var __file = $(ipt).get(0).files[0];
							formData.append('files[]', __file);
							return {
								'id' : $(ipt).closest('label').attr('data-idnama'),
								'name' : __file.name,
								'sha1' : $(ipt).attr('data-sha1'),
							};
			            }
			        });

			        var lampiran_ddp = null;
			        if ( !empty( $('input[type=file][name=lampiran_ddp]').val() )) {
						var key = $('input[type=file][name=lampiran_ddp]').attr('name');
						var __file = $('input[type=file][name=lampiran_ddp]').get(0).files[0];
						formData.append('files[]', __file);

						lampiran_ddp = {
							'id' : $('input[type=file][name=lampiran_ddp]').closest('label').attr('data-idnama'),
							'name' : __file.name,
							'sha1' : $('input[type=file][name=lampiran_ddp]').attr('data-sha1')
						};
		            }

					var banks = $.map( $(rek_pelanggan).find('tr.detail_rekening'), function(tr) {

						var ipt = $(tr).find('input:file');
						var lampiran = null;
						if ( !empty($(ipt).val()) ) {
							var __file = $(ipt).get(0).files[0];
							formData.append('files[]', __file);

							lampiran = {
								'id' : $(ipt).closest('label').attr('data-idnama'),
								'name' : __file.name,
								'sha1' : $(ipt).attr('data-sha1'),
							};
						}

						var data = {
							'nomer_rekening' : $(tr).find('input[name=rekening_plg]').val(),
							'nama_pemilik' : $(tr).find('input[name=pemilik_rekening]').val(),
							'nama_bank' : $(tr).find('input[name=bank_rekening]').val(),
							'cabang_bank' : $(tr).find('input[name=cabang_rekening]').val(),
							'lampiran' : lampiran,
						}

						return data;
					});

    				// data pelanggan
    				var jenis_pelanggan = $(div_pelanggan).find('select[name=jenis_plg]').val();
    				var nama_pelanggan = $(div_pelanggan).find('input[name=nama_plg]').val();
    				var contact_person = $(div_pelanggan).find('input[name=contact_plg]').val();
    				var platform = numeral.unformat( $(div_pelanggan).find('input[name=platform]').val() );

    				var telepons = $.map( $(div_pelanggan).find('input[name=telp_plg]'), function(ipt) {
						var telp = $(ipt).mask();
						if (!empty(telp)) {
							return telp;
						}
					});

					var ktp = $(div_pelanggan).find('input[name=ktp_plg]').mask();
					var alamat_pelanggan = {
						'kecamatan' : $(div_pelanggan).find('select[name=kecamatan_plg]').val(),
						'kelurahan' : $(div_pelanggan).find('input[name=kelurahan_plg]').val(),
						'alamat' : $(div_pelanggan).find('textarea[name=alamat_plg]').val().trim(),
						'rt' :  $(div_pelanggan).find('input[name=rt_plg]').val().trim(),
						'rw' :  $(div_pelanggan).find('input[name=rw_plg]').val().trim(),
					};
					var npwp = $(div_pelanggan).find('input[name=npwp_plg]').mask();
					var alamat_usaha = {
						'kecamatan' : $(div_pelanggan).find('select[name=kecamatan_usaha_plg]').val(),
						'kelurahan' : $(div_pelanggan).find('input[name=kelurahan_usaha_plg]').val(),
						'alamat' : $(div_pelanggan).find('textarea[name=alamat_usaha_plg]').val().trim(),
						'rt' :  $(div_pelanggan).find('input[name=rt_usaha_plg]').val().trim(),
						'rw' :  $(div_pelanggan).find('input[name=rw_usaha_plg]').val().trim(),
					};

					var data_pelanggan = {
						'jenis_pelanggan' : jenis_pelanggan,
						'ktp' : ktp,
						'nama' : nama_pelanggan,
						'cp' : contact_person,
						'npwp' : npwp,
						'telepons' : telepons,
						'alamat_pelanggan' : alamat_pelanggan,
						'alamat_usaha' : alamat_usaha,
						'banks' : banks,
						'lampirans' : lampirans,
						'lampiran_ddp' : lampiran_ddp,
						'platform' : platform
					};

					formData.append('data_pelanggan', JSON.stringify(data_pelanggan));
					plg.executeSave(formData);
					// console.log(data_pelanggan);
	    		}
    		});
		}
	}, // end - save

	executeSave : function(formData){
		var div_tab_pane = $('div.tab-pane');

		$.ajax({
			url :'parameter/Pelanggan/save',
			type : 'post',
			data : formData,
			beforeSend : function(){
				showLoading();
			},
			success : function(data){
				hideLoading();
				if(data.status){
					bootbox.alert(data.message,function() {
						plg.getLists();
						plg.load_form(data.content.id);
					});
				}else{
					bootbox.alert(data.message);
				}
			},
			contentType : false,
			processData : false,
		});
	}, // end - executeSave

	edit : function () {
		var error = 0;
		var lbl_errors = [];
		$('[required]').parent().addClass('has-error');
		$.map($('[required]'), function(elm){
			if( empty( $(elm).val() ) ){
				error++;
				lbl_errors.push( '* ' + $(elm).attr('placeholder') );
			}else{
				$(elm).parent().removeClass('has-error');
			}
		});

		if (error > 0) {
			bootbox.alert('Data belum lengkap : <br> ' + lbl_errors.join('<br>') );
		} else {
			bootbox.confirm('Apakah anda yakin data mitra akan disimpan?', function(result){
    			if (result) {
    				var div_pelanggan = $('div[name=data-pelanggan]');
    				var rek_pelanggan = $('div#rekening_pelanggan');

    				var id = $('input[type=hidden]').data('id');
    				var nomor = $('input[type=hidden]').data('nomor');
    				var status = $('input[type=hidden]').data('status');
    				var mstatus = $('input[type=hidden]').data('mstatus');
    				var version = $('input[type=hidden]').data('version');

        			var formData = new FormData();
        			var lampirans = $.map( $(div_pelanggan).find('input[type=file]'), function(ipt){
			            if (!empty( $(ipt).val() ) || !empty( $(ipt).data('old') ) ) {
							var filename = $(ipt).data('old');
							if ( !empty( $(ipt).val() ) ) {
								var __file = $(ipt).get(0).files[0];
								formData.append('files[]', __file);

								filename = __file.name;
							};

							return {
								'id' : $(ipt).closest('label').attr('data-idnama'),
								'name' : filename,
								'sha1' : $(ipt).attr('data-sha1'),
								'old' : $(ipt).data('old')
							};
			            }
			        });

			        var lampiran_ddp = null;
			        if ( !empty( $('input[type=file][name=lampiran_ddp]').val() )) {
						var key = $('input[type=file][name=lampiran_ddp]').attr('name');
						var __file = $('input[type=file][name=lampiran_ddp]').get(0).files[0];
						formData.append('files[]', __file);

						lampiran_ddp = {
							'id' : $('input[type=file][name=lampiran_ddp]').closest('label').attr('data-idnama'),
							'name' : __file.name,
							'sha1' : $('input[type=file][name=lampiran_ddp]').attr('data-sha1'),
							'old' : $('input[type=file][name=lampiran_ddp]').data('old')
						};
		            }

					var banks = $.map( $(rek_pelanggan).find('tr.detail_rekening'), function(tr) {
						var ipt = $(tr).find('input:file');
						var lampiran = null;
						if (!empty( $(ipt).val() ) || !empty( $(ipt).data('old') ) ) {
							var filename = $(ipt).data('old');
							if ( !empty( $(ipt).val() ) ) {
								var __file = $(ipt).get(0).files[0];
								formData.append('files[]', __file);

								filename = __file.name;
							};

							var lampiran = {
								'id' : $(ipt).closest('label').attr('data-idnama'),
								'name' : filename,
								'sha1' : $(ipt).attr('data-sha1'),
								'old' : $(ipt).data('old')
							};
						}

						var data = {
							'id_old' : $(tr).find('input[name=rekening_plg]').data('id'),
							'nomer_rekening' : $(tr).find('input[name=rekening_plg]').val(),
							'nama_pemilik' : $(tr).find('input[name=pemilik_rekening]').val(),
							'nama_bank' : $(tr).find('input[name=bank_rekening]').val(),
							'cabang_bank' : $(tr).find('input[name=cabang_rekening]').val(),
							'lampiran' : lampiran,
						}

						return data;
					});

    				// data pelanggan
    				var jenis_pelanggan = $(div_pelanggan).find('select[name=jenis_plg]').val();
    				var nama_pelanggan = $(div_pelanggan).find('input[name=nama_plg]').val();
    				var contact_person = $(div_pelanggan).find('input[name=contact_plg]').val();
    				var platform = numeral.unformat( $(div_pelanggan).find('input[name=platform]').val() );

    				var telepons = $.map( $(div_pelanggan).find('input[name=telp_plg]'), function(ipt) {
						var telp = $(ipt).mask();
						if (!empty(telp)) {
							return telp;
						}
					});

					var ktp = $(div_pelanggan).find('input[name=ktp_plg]').mask();
					var alamat_pelanggan = {
						'kecamatan' : $(div_pelanggan).find('select[name=kecamatan_plg]').val(),
						'kelurahan' : $(div_pelanggan).find('input[name=kelurahan_plg]').val(),
						'alamat' : $(div_pelanggan).find('textarea[name=alamat_plg]').val().trim(),
						'rt' :  $(div_pelanggan).find('input[name=rt_plg]').val().trim(),
						'rw' :  $(div_pelanggan).find('input[name=rw_plg]').val().trim(),
					};
					var npwp = $(div_pelanggan).find('input[name=npwp_plg]').mask();
					var alamat_usaha = {
						'kecamatan' : $(div_pelanggan).find('select[name=kecamatan_usaha_plg]').val(),
						'kelurahan' : $(div_pelanggan).find('input[name=kelurahan_usaha_plg]').val(),
						'alamat' : $(div_pelanggan).find('textarea[name=alamat_usaha_plg]').val().trim(),
						'rt' :  $(div_pelanggan).find('input[name=rt_usaha_plg]').val().trim(),
						'rw' :  $(div_pelanggan).find('input[name=rw_usaha_plg]').val().trim(),
					};

					var data_pelanggan = {
						'id' : id,
						'nomor' : nomor,
						'status' : status,
						'mstatus' : mstatus,
						'version' : version,
						'jenis_pelanggan' : jenis_pelanggan,
						'ktp' : ktp,
						'nama' : nama_pelanggan,
						'cp' : contact_person,
						'npwp' : npwp,
						'telepons' : telepons,
						'alamat_pelanggan' : alamat_pelanggan,
						'alamat_usaha' : alamat_usaha,
						'banks' : banks,
						'lampirans' : lampirans,
						'lampiran_ddp' : lampiran_ddp,
						'platform' : platform
					};

					formData.append('data_pelanggan', JSON.stringify(data_pelanggan));
					plg.executeEdit(formData);
					// console.log(data_pelanggan);
	    		}
    		});
		}
	}, // end - edit

	executeEdit : function(formData){
		var div_tab_pane = $('div.tab-pane');

		$.ajax({
			url :'parameter/Pelanggan/edit',
			type : 'post',
			data : formData,
			beforeSend : function(){
				showLoading();
			},
			success : function(data){
				hideLoading();
				if(data.status){
					bootbox.alert(data.message,function() {
						plg.getLists();
						plg.load_form(data.content.id);
					});
				}else{
					bootbox.alert(data.message);
				}
			},
			contentType : false,
			processData : false,
		});
	}, // end - executeEdit

	non_aktif : function (tipe = null) {
		var div_tab_pane = $('div.tab-pane');
    	var formData = new FormData();

    	var nomor = $('input[type=hidden]').data('nomor');
    	var keterangan = $('input[name=nonaktif_keterangan]').val();

    	var lampiran = $.map( $('div#form_status').find('input:file'), function(ipt){
    		if (!empty( $(ipt).val() )) {
    			var __file = $(ipt).get(0).files[0];
    			formData.append('files[]', __file);
    			return {
    				'id' : $(ipt).closest('label').attr('data-idnama'),
    				'name' : __file.name,
    				'sha1' : $(ipt).attr('data-sha1'),
    			};
    		}
    	});

    	var data = {
    		'nomor' : nomor,
    		'ket' : keterangan,
    		'lampiran' : lampiran,
    		'tipe' : tipe
    	};

    	formData.append('data', JSON.stringify(data));

    	$.ajax({
			url :'parameter/Pelanggan/nonAktif',
			type : 'post',
			data : formData,
			beforeSend : function(){
				showLoading();
			},
			success : function(data){
				hideLoading();
				if(data.status){
					bootbox.alert(data.message,function() {
						plg.getLists();
					});
				} else {
					bootbox.alert(data.message);
				}
			},
			contentType : false,
			processData : false,
		});
	}, // end - non_aktif

	ack : function () {
		var ids = $('input[type=hidden]').data('id');
		bootbox.confirm('Data mitra akan di-ack', function(result){
			if (result) {
				$.ajax({
					url : 'parameter/Pelanggan/ack',
					data : {'params' : ids},
					dataType : 'JSON',
					type : 'POST',
					beforeSend : function () {
						showLoading();
					},
					success : function(data){
						hideLoading();
						if(data.status){
							bootbox.alert(data.message,function(){
								plg.getLists();
								plg.load_form(data.content.id);
							});
						}else{
							bootbox.alert(data.message);
						}
					},
				});
			}
		});
	}, // end - ack

	addRowTable : function (elm, action) {
		var row = $(elm).closest("tr");
		var row_clone = row.clone();
		row_clone.find('select, input').val('');
		var tbody = $(elm).closest("tbody");
		tbody.append(row_clone);
		$('[data-tipe=phone]').mask("9999 9999 9??999");
		plg.setBindSHA1()
	}, // end - addRowTelepon

	removeRowTable : function (elm) {
		var row = $(elm).closest("tr");
		row.find('select, input').val('');
		if ( row.prev('tr').length > 0 || row.next('tr').length > 0 ) {
			row.remove();
		}
	}, // end - removeRowTable

    load_form_status : function(elm) {
    	var tr = $(elm).closest('tr');
    	var nomor = $(tr).find('td[name=id_pelanggan]').data('nomor');
    	var tipe = $(elm).data('tipe');

    	var title = null;
    	if ( tipe == 'aktif' ) {
    		title = 'Aktif Pelanggan'
    	} else {
    		title = 'Non Aktif Pelanggan';
    	};

    	$.ajax({
            url: 'parameter/Pelanggan/loadFormStatus',
            data: {
            	params: nomor
            },
            type: 'GET',
            dataType: 'HTML',
            success: function(html) {
            	var modal = bootbox.dialog({
            		message: html,
            		title: title,
            		buttons: [
            		{
            			label: "Simpan",
            			className: "btn btn-primary pull-right",
            			callback: function() {
            				plg.non_aktif(tipe);
            			},
            		},
            		{
            			label: "Close",
            			className: "btn btn-default pull-right",
            			callback: function() {
            				modal.modal("hide");	
            			}
            		}],
            		show: false,
            		onEscape: function() {
            			modal.modal("hide");
            		}
			    });
	    
			    modal.modal("show");
			    plg.setBindSHA1();
        	}        	
        });
    }, // end - load_form_status

    load_form_saldo : function () {
    	var tr = $(elm).closest('tr');
    	var nomor = $(tr).find('td[name=id_pelanggan]').data('nomor');

    	$.ajax({
            url: 'parameter/Pelanggan/loadFormSldAwal',
            data: {
            	params: nomor
            },
            type: 'GET',
            dataType: 'HTML',
            success: function(html) {
            	var modal = bootbox.dialog({
            		message: html,
            		title: "Saldo Awal Pelanggan",
            		buttons: [
            		{
            			label: "Simpan",
            			className: "btn btn-primary pull-right",
            			callback: function() {
            				plg.sld_awal();
            			},
            		},
            		{
            			label: "Close",
            			className: "btn btn-default pull-right",
            			callback: function() {
            				modal.modal("hide");	
            			}
            		}],
            		show: false,
            		onEscape: function() {
            			modal.modal("hide");
            		}
			    });
	    
			    modal.modal("show");
			    plg.setBindSHA1();
        	}        	
        });
    }, // end - load_form_saldo

    cari_pelanggan : function () {
    	var index = $("#filter_search").val();

    	var input, filter, table, tr, td, i, txtValue;
    	input = document.getElementById("input_cari_pelanggan");
    	filter = input.value.toUpperCase();
    	table = document.getElementById("table_pelanggan");
    	tr = table.getElementsByTagName("tr");
    	for (i = 0; i < tr.length; i++) {
    		td = tr[i].getElementsByTagName("td")[index];
    		if (td) {
    			txtValue = td.textContent || td.innerText;
    			if (txtValue.toUpperCase().indexOf(filter) > -1) {
    				tr[i].style.display = "";
    			} else {
    				tr[i].style.display = "none";
    			}
    		}
    	}
    }, // end - cari_pelanggan

    form_export_excel : function () {
		$.get('parameter/Pelanggan/form_export_excel',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
            	$(this).find('.modal-header').css({'padding-top': '0px'});
            	$(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});
            });
        },'html');
	}, // end - form_export_excel

	verifikasi_export_excel : function (elm) {
		var modal_body = $(elm).closest('div.modal-body');

		var err = 0;
		$.map( $(modal_body).find('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap isi Username dan Password terlebih dahulu.');
		} else {
			var params = {
				'username': $(modal_body).find('input[name=username]').val(),
				'password': $(modal_body).find('input[name=password]').val()
			};

			$.ajax({
				url : 'parameter/Pelanggan/verifikasi_export_excel',
				data : {'params' : params},
				dataType : 'JSON',
				type : 'POST',
				beforeSend : function () {
					showLoading();
				},
				success : function(data){
					if (data.status) {
						bootbox.hideAll();
						hideLoading();

						window.open('parameter/Pelanggan/export_excel', '_blank');
					} else {
						hideLoading();
						bootbox.alert( data.message );
					}
				}
			});
		}
	}, // end - verifikasi_export_excel
};

plg.start_up();