var solusi = {
	start_up: function () {
		solusi.get_list();
	}, // end - start_up

	get_list : function () {
		var dContent = $('tbody');

		$.ajax({
            url : 'parameter/Solusi/get_list',
            data : {},
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ App.showLoaderInContent(dContent); },
            success : function(html){
                App.hideLoaderInContent(dContent, html);
            }
        });
	}, // end - get_list

	add_form : function () {
		$.get('parameter/Solusi/add_form',{
        },function(data){
            var _options = {
                className : 'veryWidth',
				message : data,
				size : 'large',
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $('input, textarea').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });
            });
        },'html');
	}, // end - add_form

	save : function () {
		var div = $('div.modal-body');
		var err = 0;

		$(div).find('input, textarea').parent().removeClass('has-error');
		$.map( $(div).find('[data-required=1]'), function (ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data yang anda input.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin menyimpan data Solusi ?', function (result) {
				if ( result ) {
					var keterangan = $(div).find('textarea.ket').val();

					// console.log( keterangan );
					solusi.execute_save(keterangan);
				};
			});
		};
	}, // end - save

	execute_save : function (params = null) {
		$.ajax({
            url : 'parameter/Solusi/save',
            data : {'params' : params},
            type : 'POST',
            dataType : 'JSON',
            beforeSend : function(){ showLoading(); },
            success : function(data){
                hideLoading();
                if (data.status) {
                    bootbox.alert(data.message, function(){
                        solusi.get_list();
                        bootbox.hideAll();
                    });
                } else {
                    alertDialog(data.message);
                }
            }
        });
	}, // end - execute_save

	edit_form : function (elm) {
		var id = $(elm).data('id');
		$.get('parameter/Solusi/edit_form',{
			'id' : id
        },function(data){
            var _options = {
                className : 'veryWidth',
				message : data,
				size : 'large',
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });
            });
        },'html');
	}, // end - edit_form

	edit : function (elm) {
		var div = $('div.modal-body');
		var err = 0;

		$(div).find('input, textarea').parent().removeClass('has-error');
		$.map( $(div).find('[data-required=1]'), function (ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data yang anda input.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin mengupdate data Solusi ?', function (result) {
				if ( result ) {
					var keterangan = $(div).find('textarea.ket').val();

					var params = {
						'id' : $(elm).data('id'),
						'keterangan' : keterangan
					};

					// console.log( keterangan );
					solusi.execute_edit(params);
				};
			});
		};
	}, // end - edit

	execute_edit : function (params) {
		$.ajax({
            url : 'parameter/Solusi/edit',
            data : {'params' : params},
            type : 'POST',
            dataType : 'JSON',
            beforeSend : function(){ showLoading(); },
            success : function(data){
                hideLoading();
                if (data.status) {
                    bootbox.alert(data.message, function(){
                        solusi.get_list();
                        bootbox.hideAll();
                    });
                } else {
                    alertDialog(data.message);
                }
            }
        });
	}, // end - execute_edit
};

solusi.start_up();
