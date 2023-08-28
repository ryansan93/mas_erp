var kk = {
	startUp: function () {
		kk.settingUp();
	}, // end - startUp

	settingUp: function () {
		$('.unit').select2({placeholder: 'Pilih Unit'}).on("select2:select", function (e) {
            var unit = $('.unit').select2('val');

            $('.btn-tutup-bulan').addClass('hide');
        });

		$('.datetimepicker').datetimepicker({
            locale: 'id',
            format: 'MMM YYYY'
        });
	}, // end - settingUp

	getLists: function () {
		var err = 0;

		$.map( $('[data-required=1]'), function (ipt) {
			if ( empty( $(ipt).val() ) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			var unit = $('.unit').select2('val');

			var params = {
				'unit': unit,
				'periode': dateSQL( $('#periode').data('DateTimePicker').date() )
			};

			$.ajax({
                url : 'report/KasKecil/getLists',
                data : {
                    'params' : params
                },
                type : 'POST',
                dataType : 'JSON',
                beforeSend : function(){ showLoading(); },
                success : function(data){
                	hideLoading();

                	if ( data.status == 1 ) {
                    	$('table').find('tbody').html( data.html );
                    	$('.btn-tutup-bulan').attr('data-status', data.status_btn_tutup_bulan);
                    	kk.cekBtnTutupBulan( unit );
                	} else {
                		bootbox.alert( data.message );
                	}
                }
            });
		}
	}, // end - getLists

	cekBtnTutupBulan: function (unit) {
		var status = $('.btn-tutup-bulan').attr('data-status');

		if ( status == 1 && unit != 'all' ) {
			$('.btn-tutup-bulan').removeClass('hide');
		} else {
			$('.btn-tutup-bulan').addClass('hide');
		}
	}, // end - cekBtnTutupBulan

	save: function () {
		bootbox.confirm('Apakah anda yakin ingin menutup bulan kas kecil ?', function (result) {
			if ( result ) {
				var params = {
					'unit': $('.unit').select2('val'),
					'periode': dateSQL( $('#periode').data('DateTimePicker').date() ),
					'saldo_akhir': numeral.unformat( $('.saldo_akhir').find('b').text() )
				};
				
				$.ajax({
	                url : 'report/KasKecil/save',
	                data : {
	                    'params' : params
	                },
	                type : 'POST',
	                dataType : 'JSON',
	                beforeSend : function(){ showLoading(); },
	                success : function(data){
	                	hideLoading();

	                	if ( data.status == 1 ) {
	                    	bootbox.alert( data.message, function () {
	                    		kk.getLists();
	                    	});
	                	} else {
	                		bootbox.alert( data.message );
	                	}
	                }
	            });
			}
		});
	}, // end - save
};

kk.startUp();