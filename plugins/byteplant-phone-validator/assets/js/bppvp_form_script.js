(function($){
	"use strict";
	var app = {
		el: '',
		msgContainer: false,
		validPhones: [],

		submit: function(){
			var self = this;
			$(self.el).trigger('submit');
		},

		request: function(phone){
			if(!$('body').hasClass('bppvp-loading')){
				var self = this;

				$('body').addClass('bppvp-loading');

				var inputs = $(self.el).find('.bppvp-phone');
				_.each($(inputs),function(el,key){
					if($(el).val()==phone){
						$(el).addClass('bppvp-loading-phone');
					}
				});

				if(phone.length>1){
					$.ajax({
						url: bppvp.AJAX_URL,
						type: 'POST',
						cache: false,
						crossDomain: true,
						data: {
							'action'     : 'bppy-verify-phone',
							'bppv-nonce' : bppvp.nonce,
							'number'     : phone
                        },
						dataType: 'json',
						success: function (data) {
							$('body').removeClass('bppvp-loading');
							var inputs = $(self.el).find('.bppvp-phone');
							_.each($(inputs),function(el,key){
								if($(el).val()==phone){
									$(el).removeClass('bppvp-loading-phone');
								}
							});

							if( data.is_valid != false ){
								self.validPhones.push( phone );
								self.submit();
							} else {
								self.showError(phone, data.status);
							}
						},
						error: function(){
							$('body').removeClass('bppvp-loading');
						}
					});
				} else {
					$('body').removeClass('bppvp-loading');
					self.showError(phone, 'UNKNOWN' );
				}
			}
		},
		validateForm: function( form ){
			var self = this,
				inputs = $(form).find('.bppvp-phone');

			self.el = form;

			if( $(self.el).find('.wpcf7-response-output').length ){
				self.msgContainer = $(self.el).find('.wpcf7-response-output');
			} else {
				self.msgContainer = false;
			}

			var phones = [],
				inputs = $(form).find('.bppvp-phone');

			_.each( inputs, function(el, key){
				phones.push( $(inputs[key]).val() );
			});

			if( _.isEmpty( _.difference( phones, app.validPhones ) ) ){
				self.submit();
			} else {
				var phone = _.difference( phones, app.validPhones );
				self.request( phone[0] );
			}


		},
		showError: function(phone, status){
			var self = this;
			if(self.msgContainer){
				var template = _.template( bppvp.tpl );
				$(self.msgContainer).html( template( {phone:phone, status: status} ) ).show();
			} else {
				var inputs = $(self.el).find('.bppvp-phone');
				_.each($(inputs),function(el,key){
					if($(el).val()==phone){
						var template = _.template( bppvp.tpl );
						$(el).after( template( {phone:phone, status: status} ) );
					}
				});
			}
		}
	};

	$(document).ready(function(){
		if($('form').length){
			

			_.each( $('form'), function( form, key ){
				if($(form).find('.bppvp-phone').length){
					$(form).on('submit', function(){
						var phones = [],
							inputs = $(form).find('.bppvp-phone');

						$(form).find('.bppvp-error').remove();

						_.each( inputs, function(el, key){
							phones.push( $(inputs[key]).val() );
						});

						if( _.isEmpty( _.difference( phones, app.validPhones ) ) ){
							$(form).removeClass( bppvp.form_class );
							return true;
						} else {
							app.validateForm( $(form) );
						}

						return false;
					});
				}
			});
		}
	});
}(jQuery));