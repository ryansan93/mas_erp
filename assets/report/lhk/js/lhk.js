var lhk = {
	start_up: function () {
        // lhk.set_table_page('.tbl_ktp');
        lhk.setting_up();
	},

 	// set_table_page : function(tbl_id){
  //       let _t_rdim = TUPageTable;
  //       _t_rdim.destroy();
  //       _t_rdim.setTableTarget(tbl_id);
  //       _t_rdim.setPages(['page1', 'page2']);
  //       _t_rdim.setHideButton(true);
  //       _t_rdim.onClickNext(function(){
  //           // console.log('Log onClickNext');
  //       });
  //       _t_rdim.start();
  //   }, // end - set_table_page

	setting_up: function() {
		$('#select_mitra').selectpicker();
		$('#select_mitra').on('changed.bs.select', function (e, clickedIndex, newValue, oldValue) {
		    lhk.get_noreg();
		});

		$('#select_noreg').selectpicker();

		$('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
			$(this).priceFormat(Config[$(this).data('tipe')]);
		});
	}, // end - setting_up

	get_noreg: function() {
    	var nomor_mitra = $('#select_mitra').val();

    	var option = '<option value="">Pilih Noreg</option>';
    	if ( !empty(nomor_mitra) ) {
    		$.ajax({
	            url: 'report/LHK/get_noreg',
	            data: { 'params': nomor_mitra },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function(){ showLoading() },
	            success: function(data){
	                var noreg = $('select#select_noreg').data('val');
	                if ( data.content.length > 0 ) {
	                	for (var i = 0; i < data.content.length; i++) {
	                		var selected = null;
	                		if ( data.content[i].noreg == noreg ) {
	                			selected = 'selected';
	                		}
	                		option += '<option data-tokens="'+data.content[i].tgl_docin+' | '+data.content[i].kandang+' | '+data.content[i].noreg+'" data-umur="'+data.content[i].umur+'" data-tgldocin="'+data.content[i].real_tgl_docin+'" data-tgllhkterakhir="'+data.content[i].tgl_lhk_terakhir+'" value="'+data.content[i].noreg+'" '+selected+'>'+data.content[i].tgl_docin+' | '+data.content[i].kandang+' | '+data.content[i].noreg+'</option>';
	                	}
	                }
	                $('select#select_noreg').removeAttr('disabled');
	                $('select#select_noreg').html(option);
	                $('#select_noreg').selectpicker('refresh');

	                hideLoading();
	            }
	        });
    	} else {
    		$('select#select_noreg').attr('disabled', 'disabled');
    		$('select#select_noreg').html(option);
    		$('#select_noreg').selectpicker('refresh');
    	}
    }, // end - get_noreg

	get_lists: function () {
		var err = 0;

		$.map( $('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				err++;
				$(ipt).parent().addClass('has-error');
			} else {
				$(ipt).parent().removeClass('has-error');
			};
		});

		if ( err > 1 ) {
			bootbox.alert('Data belum lengkap, mohon cek kembali data yang anda masukkan.');
		} else {
			var params = {
				'noreg' : $('#select_noreg').val()
			};

			$.ajax({
	            url : 'report/LHK/get_lists',
	            data : {
	            	'params' : params
	            },
	            dataType : 'JSON',
	            type : 'POST',
	            beforeSend : function(){
	            	showLoading();
	            },
	            success : function(data){
	                $('table.tbl_lhk tbody').html(data.content);

	                // lhk.set_table_page('.tbl_ktp');

	                hideLoading();
	            }
	        });
		};
	}, // end - get_lists

	preview_file_attachment: function(elm) {
    	var div_attachment = $(elm).prev('div.attachment');

    	var data_url = $(elm).attr('data-url');

		var judul = $(elm).data('title');
		var _url = [];
    	if ( empty(data_url) ) {
    		if ( $(div_attachment).length > 0 ) {
		    	var files = $(div_attachment).find('.file_lampiran')[0].files;
		    	
				for (var i = 0; i < files.length; i++) {
					_temp_url = URL.createObjectURL(files[i]);

					_url.push(_temp_url);
				}
    		}
    	} else {
    		var _data_url = JSON.parse(data_url);
    		for(var i in _data_url) {
    			_url.push('uploads/'+_data_url[i]);
    		}
    	}

    	if ( _url.length > 0 ) {
			$.get('report/LHK/preview_file_attachment',{
					'params': _url,
					'judul': judul
				},function(data){
				var _options = {
					className : 'veryWidth',
					message : data,
					size : 'large',
				};
				bootbox.dialog(_options).bind('shown.bs.modal', function(){
					var modal_body = $(this).find('.modal-body');
					var table = $(modal_body).find('table');
					var tbody = $(table).find('tbody');
					if ( $(tbody).find('.modal-body tr').length <= 1 ) {
				        $(this).find('tr #btn-remove').addClass('hide');
				    };

				    $(this).find('button.close').click(function() {
				    	$('div.modal.show').css({'overflow': 'auto'});
				    });
				});
			},'html');
    	} else {
    		bootbox.alert('Tidak ada file yang akan di tampilkan.');
    	}
	}, // end - preview_file_attachment

	nekropsi: function(elm) {
    	var id = $(elm).data('id');

		$.get('report/LHK/nekropsi',{
				'id': id
			},function(data){
			var _options = {
				className : 'veryWidth',
				message : data,
				size : 'large',
			};
			bootbox.dialog(_options).bind('shown.bs.modal', function(){
				var modal_body = $(this).find('.modal-body');
				var table = $(modal_body).find('table');
				var tbody = $(table).find('tbody');
				if ( $(tbody).find('.modal-body tr').length <= 1 ) {
			        $(this).find('tr #btn-remove').addClass('hide');
			    };

			    $(this).find('button.close').click(function() {
			    	$('div.modal.show').css({'overflow': 'auto'});
			    });
			});
		},'html');
	}, // end - nekropsi

	solusi: function(elm) {
    	var id = $(elm).data('id');

		$.get('report/LHK/solusi',{
				'id': id
			},function(data){
			var _options = {
				className : 'veryWidth',
				message : data,
				size : 'large',
			};
			bootbox.dialog(_options).bind('shown.bs.modal', function(){
				var modal_body = $(this).find('.modal-body');
				var table = $(modal_body).find('table');
				var tbody = $(table).find('tbody');
				if ( $(tbody).find('.modal-body tr').length <= 1 ) {
			        $(this).find('tr #btn-remove').addClass('hide');
			    };

			    $(this).find('button.close').click(function() {
			    	$('div.modal.show').css({'overflow': 'auto'});
			    });
			});
		},'html');
	}, // end - solusi

	sekat: function(elm) {
    	var id = $(elm).data('id');

		$.get('report/LHK/sekat',{
				'id': id
			},function(data){
			var _options = {
				className : 'veryWidth',
				message : data,
				size : 'large',
			};
			bootbox.dialog(_options).bind('shown.bs.modal', function(){
				var modal_body = $(this).find('.modal-body');
				var modal_content = $(modal_body).closest('.modal-content');
				var modal_dialog = $(modal_content).closest('.modal-dialog');

				$(modal_dialog).css({'width': '30%'})

				var table = $(modal_body).find('table');
				var tbody = $(table).find('tbody');
				if ( $(tbody).find('.modal-body tr').length <= 1 ) {
			        $(this).find('tr #btn-remove').addClass('hide');
			    };

			    $(this).find('button.close').click(function() {
			    	$('div.modal.show').css({'overflow': 'auto'});
			    });
			});
		},'html');
	}, // end - sekat
};

lhk.start_up();