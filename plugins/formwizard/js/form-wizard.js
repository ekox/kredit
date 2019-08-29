var FormWizard = function () {

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            $("#stsbpjs,#stsdomisili,#is_alamat_ktp,#is_alamat_pemohon,#is_huni,#kdjenkredit, #kdtipe, #id_hunian, #id_form, #kdkelamin, #kdagama, #kdpendidikan, #kdpekerjaan, #kdkawin, #kdbpjs, #kdprop, #kdkabkota, #kdkec, #kdkel, #kdprop1, #kdkabkota1, #kdkec1, #kdkel1, #kdkelamin_p, #kdagama_p, #kdpendidikan_p, #kdpekerjaan_p, #kdprop_p, #kdkabkota_p, #kdkec_p, #kdkel_p, #kdhutang, #kdkreditur").select2({
                placeholder: "Select",
                allowClear: true,
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
					kdjenkredit: {
						required: true
					},
					kdtipe: {
						required: true
					},
					/*id_form: {
						required: true
					},*/
					nik: {
						required: true
					},
					/*nik_p: {
						required: true
					},*/
					npwp: {
						required: true
					},
					nokk: {
						required: true
					},
					nama: {
						required: true
					},
					stsbpjs: {
						required: true
					},
					stsdomisili: {
						required: true
					},
					/*nama_p: {
						required: true
					},*/
					kotlhr: {
						required: true
					},
					tglhr: {
						required: true
					},
					nmibu: {
						required: true
					},
					kdkelamin: {
						required: true
					},
					kdagama: {
						required: true
					},
					kdpendidikan: {
						required: true
					},
					kdkawin: {
						required: true
					},
					kdbpjs: {
						required: true
					},
					nohp: {
						required: true
					},
					/*nohp_p: {
						required: true
					},*/
					email: {
						required: true
					},
					kdprop: {
						required: true
					},
					kdkabkota: {
						required: true
					},
					kdkec: {
						required: true
					},
					kdkel: {
						required: true
					},
					kodepos: {
						required: true
					},
					telp: {
						required: true
					},
					alamat: {
						required: true
					},
					
					/*kdprop1: {
						required: true
					},
					kdkabkota1: {
						required: true
					},
					kdkec1: {
						required: true
					},
					kdkel1: {
						required: true
					},
					kodepos1: {
						required: true
					},
					telp1: {
						required: true
					},
					alamat1: {
						required: true
					},*/
					
					kdpekerjaan: {
						required: true
					},
					lama_k: {
						required: true
					},
					nmkantor: {
						required: true
					},
					bidang: {
						required: true
					},
					jenis: {
						required: true
					},
					alamat_k: {
						required: true
					},
					/*jabatan: {
						required: true
					},
					atasan: {
						required: true
					},
					telp_k: {
						required: true
					},*/
					tgkerja: {
						required: true
					},
					penghasilan: {
						required: true
					},
					penghasilan1: {
						required: true
					},
					
					jmlkjp: {
						required: true
					},
					jmlbpjs: {
						required: true
					},
					jmltanggung: {
						required: true
					},
					jmlrmh: {
						required: true
					},
					jmlroda2: {
						required: true
					},
					jmlroda4: {
						required: true
					},
					pengeluaran: {
						required: true
					},
					jmltinggal: {
						required: true
					},
					
					pin_ktp: {
						required: true,
						minlength:16,
						maxlength:16,
						number: true,
						remote: {  // value of 'username' field is sent by default
							type: 'get',
							url: 'cek-pin-ktp'
						}
					},
					
					email: {
						required: true,
						email: true
					},
					
					tgpemohon: {
						required: true
					},
					
					//disclaimer
					'agree[]': {
						required: true,
						minlength: 1
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
	
					pin_nip:{
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

                /*submitHandler: function (form) {
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
                }*/
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

            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1 .button-submit').click(function () {
                if (form.valid() == true) {
					$('#form_wizard_1 .button-submit').prop('disabled', true).html('Sedang proses.....');
                    var data=jQuery('#form-ruh').serialize();
					
					localStorage.setItem("debitur", data);
					
					jQuery.ajax({
						url:'',
						method:'POST',
						data:data,
						success:function(result){
							
							$('#form_wizard_1 .button-submit').prop('disabled', false).html('Submit <i class="m-icon-swapright m-icon-white"></i>');
							
							if(result=='success'){
								alertify.log('Data berhasil disimpan!');
								window.open('tanda-terima/'+jQuery('#nik').val(), '_blank');
							}
							else{
								alertify.log(result);
							}
							
						},
						error:function(result){
							alertify.log(result);
							$('#form_wizard_1 .button-submit').prop('disabled', false).html('Submit <i class="m-icon-swapright m-icon-white"></i>');
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