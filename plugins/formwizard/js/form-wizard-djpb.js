var FormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            /*function format(state) {
                if (!state.id) return state.text; // optgroup
                return "<img class='flag' src='../../assets/global/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
            }*/

            $("#kdsatker").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdkppn").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdnip").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdlevel").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdkelamin").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdkota1").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdprov1").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdkota2").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdprov2").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdkota3").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdprov3").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#jendok").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdkw").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#thang").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#negdom").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			/*$("#nip").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });*/
			
			$("#negktp").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			$("#kdjabatan").select2({
                placeholder: "Select",
                allowClear: true,
                //formatResult: format,
                //formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
			
			var form = $('#form-ruh');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
					//data kppn
					kdkppn: {
						required: true
					},
					
					//data satker
					kddept: {
						required: true
					},
					kdunit: {
						required: true
					},
					kdsatker: {
						required: true
					},
					nodipa: {
						required: true
					},
					tgdipa: {
						required: true
					},
					kdkota3: {
						required: true
					},
					
					kdprov3: {
						required: true
					},
					
					jendok: {
						required: true
					},
					
					kdkw: {
						required: true
					},
					
					nokarwas: {
						required: true
					},
					
					thang: {
						required: true
					},
					
					kdpos3: {
						required: true,
						minlength:5,
						maxlength:5,
						number: true
					},
					
					almsatker: {
						required: true
					},
					
					telpkantor: {
						required: true,
						/* number: true */
					},
					
					faxkantor: {
						required: true,
						/* number: true */
					},
					
					//data user
					nama: {
						required: true
					},
					
					ktp: {
						required: true,
						minlength:16,
						maxlength:16,
						number: true,
						/* remote: {  // value of 'username' field is sent by default
							type: 'get',
							url: 'cek-ktp'
						} */
					},

					pin_ktp: {
						required: true,
						minlength:16,
						maxlength:16,
						number: true,
						remote: {  // value of 'username' field is sent by default
							type: 'get',
							url: 'cek-djpb-ktp'
						}
					},
					
					tptlahir: {
						required: true
					},
					
					tgllahir: {
						required: true
					},
					
					kdkelamin: {
						required: true
					},
					
					kdnip: {
						required: true,
					},

					nip: {
						required: true,
						//maxlength: 18,
						//minlength: 18,
						//number: true,
						remote: {  // value of 'username' field is sent by default
							type: 'get',
							url: 'cek-djpb-nip'
						}
					},
					
					nip2: {
						required: true
					},
					
					jabatan: {
						required: true
					},
					
					nmibu: {
						required: true
					},
					
					email: {
						required: true,
						djpbEmail: true
					},
					
					telp: {
						required: true,
						number: true
					},
					
					telpsel: {
						required: true,
						number: true
					},
					
					kdkota1: {
						required: true
					},
					
					kdprov1: {
						required: true
					},
					
					kdpos1: {
						required: true,
						minlength:5,
						maxlength:5,
						number: true
					},
					
					kdkota2: {
						required: true
					},
					
					kdprov2: {
						required: true
					},
					
					negdom: {
						required: true
					},
					
					negktp: {
						required: true
					},
					
					almdom: {
						required: true
					},
					
					almktp: {
						required: true
					},
					
					kdpos2: {
						required: true,
						minlength:5,
						maxlength:5,
						number: true
					},
					
					/*kips: {
						required: true,
						minlength:16,
						maxlength:16,
						number: true
					},*/
					
					kdlevel: {
						required: true
					},
					
					//data login
					username: {
						required: true,
						minlength: 6,
						remote: {  // value of 'username' field is sent by default
							type: 'get',
							url: 'cek-username'
						}
					},
					
					password: {
						required: true,
						minlength: 8,
						pwcheck: true
					},
					
					password1: {
						required: true,
						equalTo: "#password"
					},
					
					//upload
					upload3: {
						required: true,
						extension: "png|jpg",
						filesize: 1048576
					},
					
					upload4: {
						required: true,
						extension: "png|jpg",
						filesize: 1048576
					},
					
					upload5: {
						required: true,
						extension: "png|jpg",
						filesize: 1048576,
						remote: {  // value of 'username' field is sent by default
							type: 'post',
							url: 'registrasi/upload/ktp-pinppspm/{kdsatker}'
						}
					},
					
					upload1: {
						required: true,
						extension: "pdf",
						filesize: 1048576
					},
					
					upload2: {
						required: true,
						extension: "pdf",
						filesize: 1048576
					},
					
					ttdsk: {
						required: true
					},
					
					nosk: {
						required: true
					},
					
					tgsk: {
						required: true
					},
					
					//disclaimer
					'agree[]': {
						required: true,
						minlength: 1
					},
					
					kdjabatan: {
						required: true
					}
                },
				
				messages: { // custom messages for radio buttons and checkboxes
                    'agree[]': {
                        required: "Silahkan beri checklist persetujuan tanggung jawab terlebih dahulu",
                        minlength: jQuery.validator.format("Silahkan beri checklist persetujuan tanggung jawab terlebih dahulu")
                    },
					
					username:{
						remote: "Username telah terdaftar"
					},
					
					/* nip:{
						remote: "NIP telah terdaftar"
					}, */
	
					nip:{
						remote: "NIP telah terdaftar"
					},
					
					kdpos1:{
						minlength: "Masukkan 5 digit kode pos"
					},

					kdpos2:{
						minlength: "Masukkan 5 digit kode pos"
					},

					kdpos3:{
						minlength: "Masukkan 5 digit kode pos"
					},

					ktp:{
						minlength: "Masukkan 16 digit nomor KTP",
						/* remote: "NIK telah terdaftar" */
					},

					pin_ktp:{
						minlength: "Masukkan 16 digit nomor KTP",
						remote: "NIK telah terdaftar"
					},
					
					kips:{
						minlength: "Masukkan 16 digit nomor KIPS"
					},
					
					upload3:{
						required: "Lakukan upload file terlebih dahulu untuk melanjutkan",
						extension: "Tipe file ekstensi tidak sesuai .png",
						filesize: "Ukuran file melebihi 1 MB"
					},
					
					upload4:{
						required: "Lakukan upload file terlebih dahulu untuk melanjutkan",
						extension: "Tipe file ekstensi tidak sesuai .png",
						filesize: "Ukuran file melebihi 1 MB"
					},
					
					upload5:{
						required: "Lakukan upload file terlebih dahulu untuk melanjutkan",
						extension: "Tipe file ekstensi tidak sesuai .png",
						filesize: "Ukuran file melebihi 1 MB",
						remote: "Proses upload gagal"
					},
					
					upload2:{
						required: "Lakukan upload file terlebih dahulu untuk melanjutkan",
						extension: "Tipe file ekstensi tidak sesuai .pdf",
						filesize: "Ukuran file melebihi 1 MB"
					},
					
					upload1:{
						required: "Lakukan upload file terlebih dahulu untuk melanjutkan",
						extension: "Tipe file ekstensi tidak sesuai .pdf",
						filesize: "Ukuran file melebihi 1 MB"
					}
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "agree[]") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter("#form_agree_error");
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    Metronic.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    if (label.attr("for") == "agree[]") { // for checkboxes and radio buttons, no need to show OK icon
                        label
							.addClass('valid')
                            .closest('.form-group').removeClass('has-error').addClass('has-success');
                        label.remove(); // remove error label here
                    } else { // display success icon for other inputs
                        label
                            .addClass('valid') // mark the current input as valid and display OK icon
                        .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    }
                },

                submitHandler: function (form) {
					var data=jQuery('#form-ruh').serialize();
					jQuery.ajax({
						url:'registrasi',
						method:'POST',
						data:data,
						success:function(result){
							console.log(result);
							if(typeof(result)==='number'){
								
								//download cetakan
								window.open('registrasi/cetak/'+result, '_blank');
								
								//tembak email
								jQuery.get('registrasi/email/'+result, function(e){
									if(e){
										alertify.log('Proses pengiriman notifikasi ke email berhasil!');
									}
									else{
										alertify.log('Proses pengiriman notifikasi ke email gagal!');
									}
								});
								
							}
							else{
								alertify.log(result);
							}
						},
						error:function(result){
							alertify.log(result);
						}
					});
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }
            });

            var handleTitle = function(tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_1')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current == 1) {
                    $('#form_wizard_1').find('.button-previous').hide();
                } else {
                    $('#form_wizard_1').find('.button-previous').show();
                }

                if (current >= total) {
                    $('#form_wizard_1').find('.button-next').hide();
                    $('#form_wizard_1').find('.button-submit').show();
                } else {
                    $('#form_wizard_1').find('.button-next').show();
                    $('#form_wizard_1').find('.button-submit').hide();
                }
                Metronic.scrollTo($('.page-title'));
            }
			
			var handleTitlePin = function(tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#form_wizard_pin')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_pin')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current == 1) {
                    $('#form_wizard_pin').find('.button-previous').hide();
                } else {
                    $('#form_wizard_pin').find('.button-previous').show();
                }

                if (current >= total) {
                    $('#form_wizard_pin').find('.button-next').hide();
                    $('#form_wizard_pin').find('.button-submit').show();
                } else {
                    $('#form_wizard_pin').find('.button-next').show();
                    $('#form_wizard_pin').find('.button-submit').hide();
                }
                Metronic.scrollTo($('.page-title'));
            }
			
			var handleTitleDjpb = function(tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#form_wizard_djpb')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_djpb')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current == 1) {
                    $('#form_wizard_djpb').find('.button-previous').hide();
                } else {
                    $('#form_wizard_djpb').find('.button-previous').show();
                }

                if (current >= total) {
                    $('#form_wizard_djpb').find('.button-next').hide();
                    $('#form_wizard_djpb').find('.button-submit').show();
                } else {
                    $('#form_wizard_djpb').find('.button-next').show();
                    $('#form_wizard_djpb').find('.button-submit').hide();
                }
                Metronic.scrollTo($('.page-title'));
            }

            // default form wizard
            $('#form_wizard_1').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {
                    return false;
                    /*
                    success.hide();
                    error.hide();
                    if (form.valid() == false) {
                        return false;
                    }
                    handleTitle(tab, navigation, clickedIndex);
                    */
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }

                    handleTitle(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitle(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard_1').find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });
			
			// default form wizard pin
            $('#form_wizard_pin').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {
                    return false;
                    /*
                    success.hide();
                    error.hide();
                    if (form.valid() == false) {
                        return false;
                    }
                    handleTitle(tab, navigation, clickedIndex);
                    */
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }

                    handleTitlePin(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitlePin(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard_pin').find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });
			
			// default form wizard djpb
            $('#form_wizard_djpb').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {
                    return false;
                    /*
                    success.hide();
                    error.hide();
                    if (form.valid() == false) {
                        return false;
                    }
                    handleTitle(tab, navigation, clickedIndex);
                    */
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }

                    handleTitleDjpb(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitleDjpb(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard_djpb').find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1 .button-submit').click(function () {
                if (form.valid() == true) {
					$('#form_wizard_1 .button-submit').prop('disabled', true).html('Sedang proses.....');
                    var data=jQuery('#form-ruh').serialize();
					jQuery.ajax({
						url:'registrasi',
						method:'POST',
						data:data,
						success:function(result){
							
							console.log(result);
							if(parseInt(result)){
								
								$('#form_wizard_1 .button-submit').prop('disabled', false).html('Submit <i class="m-icon-swapright m-icon-white"></i>');
								
								jQuery('#div-selesai').html(
									'<a href="registrasi/notif/'+result+'" target="_blank" class="btn btn-danger btn-default">Notifikasi</a>'+
									'<a href="registrasi/cetak/'+result+'" target="_blank" class="btn btn-primary btn-default">Disclaimer</a>'
								);
								
								jQuery('#modal-registrasi').modal('show');
								
								//download cetakan
								window.open('registrasi/cetak/'+result, '_blank');
								
								//tembak email
								jQuery.get('registrasi/notif/'+result, function(e){
									if(e){
										alertify.log('Proses pengiriman notifikasi ke email berhasil!');
									}
									else{
										alertify.log('Proses pengiriman notifikasi ke email gagal!');
									}
								});
								
							}
							else{
								alertify.log(result);
								$('#form_wizard_1 .button-submit').prop('disabled', false).html('Submit <i class="m-icon-swapright m-icon-white"></i>');
							}
							
						},
						error:function(result){
							alertify.log(result);
							$('#form_wizard_1 .button-submit').prop('disabled', false).html('Submit <i class="m-icon-swapright m-icon-white"></i>');
						}
					});
                }
				
            }).hide();
			
			$('#form_wizard_pin').find('.button-previous').hide();
            $('#form_wizard_pin .button-submit').click(function () {
				var thisid = jQuery(this);
                if (form.valid() == true) {
					thisid.addClass('disabled').html('Sedang proses.....');
                    var data=jQuery('#form-ruh').serialize();
					jQuery.ajax({
						url:'registrasi-pin-tambah',
						method:'POST',
						data:data,
						success:function(result){
							
							console.log(result);
							if(parseInt(result)){
								
								thisid.removeClass('disabled').html('Submit <i class="m-icon-swapright m-icon-white"></i>');
								
								jQuery('#div-selesai').html(
									'<a href="registrasi-pin/notif/'+result+'" target="_blank" class="btn btn-danger btn-default">Notifikasi</a>'+
									'<a href="registrasi-pin/cetak/'+result+'" target="_blank" class="btn btn-primary btn-default">Disclaimer</a>'
								);
								
								jQuery('#modal-registrasi').modal('show');
								
								//download cetakan
								window.open('registrasi-pin/cetak/'+result, '_blank');
								
								//tembak email
								jQuery.get('registrasi-pin/notif/'+result, function(e){
									if(e){
										alertify.log('Proses pengiriman notifikasi ke email berhasil!');
									}
									else{
										alertify.log('Proses pengiriman notifikasi ke email gagal!');
									}
								});
								
							}
							else{
								alertify.log(result);
								thisid.removeClass('disabled').html('Submit <i class="m-icon-swapright m-icon-white"></i>');
							}
							
						},
						error:function(result){
							alertify.log('Koneksi terputus!');
							thisid.removeClass('disabled').html('Submit <i class="m-icon-swapright m-icon-white"></i>');
						}
					});
                }
				
            }).hide();
			
			$('#form_wizard_djpb').find('.button-previous').hide();
            $('#form_wizard_djpb .button-submit').click(function () {
				var thisid = jQuery(this);
                if (form.valid() == true) {
					thisid.addClass('disabled').html('Sedang proses.....');
                    var data=jQuery('#form-ruh').serialize();
					jQuery.ajax({
						url:'registrasi-djpb-tambah',
						method:'POST',
						data:data,
						success:function(result){
							
							console.log(result);
							if(parseInt(result)){
								
								thisid.removeClass('disabled').html('Submit <i class="m-icon-swapright m-icon-white"></i>');
								
								jQuery('#div-selesai').html(
									'<a href="registrasi-djpb/notif/'+result+'" target="_blank" class="btn btn-danger btn-default">Notifikasi</a>'+
									'<a href="registrasi-djpb/cetak/'+result+'" target="_blank" class="btn btn-primary btn-default">Disclaimer</a>'
								);
								
								jQuery('#modal-registrasi').modal('show');
								
								//download cetakan
								window.open('registrasi-djpb/cetak/'+result, '_blank');
								
								//tembak email
								jQuery.get('registrasi-djpb/notif/'+result, function(e){
									if(e){
										alertify.log('Proses pengiriman notifikasi ke email berhasil!');
									}
									else{
										alertify.log('Proses pengiriman notifikasi ke email gagal!');
									}
								});
								
							}
							else{
								alertify.log(result);
								thisid.removeClass('disabled').html('Submit <i class="m-icon-swapright m-icon-white"></i>');
							}
							
						},
						error:function(result){
							alertify.log('Koneksi terputus!');
							thisid.removeClass('disabled').html('Submit <i class="m-icon-swapright m-icon-white"></i>');
						}
					});
                }
				
            }).hide();

            //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
			
			$('#kdsatker', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdkppn', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			$('#kdnip', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdlevel', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdkelamin', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdjabatan', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdkota1', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdprov1', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdkota2', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdprov2', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdkota3', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdprov3', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#jendok', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#kdkw', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#thang', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#negdom', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
			
			$('#negktp', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
        }

    };

}();