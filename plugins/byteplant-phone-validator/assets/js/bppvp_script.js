(function($){
	"use strict";
	var app = {
		elMsg: $('#bppvp-message'),
		elList: $('#bppvp-list'),
		show: function(){
			$(this.elMsg).show();
		},
		hide: function(){
			$(this.elMsg).hide();
		},
		empty: function(){
			$(this.elMsg).html('');

			return this.hide();
		},
		setMessage: function(text){
			$(this.elMsg).html(text);

			return this.show();
		},
		append: function(phone,status){
			var compiled = _.template( bppvp.ul_tpl );
			$(this.elList).append( compiled( {data:{phone:phone, status: bppvp[status] } } ) );
		},
		request: function(phone){
			if(!$('body').hasClass('bppvp-loading')){
				var self = this;

				$('body').addClass('bppvp-loading');

				$.ajax({
					url: bppvp['AJAX_URL'],
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
						self.append( phone, data.status );
					},
					error: function(){
						$('body').removeClass('bppvp-loading');
						self.setMessage(bppvp[801]);
					}
				});
			}
		}
	};

	$(document).ready(function(){
		$('#bppvp-button-validate').on('click',function(){
			if($('#bppvp-phone').val().length){
				app.hide();
				app.request( $('#bppvp-phone').val() );
			} else {
				app.setMessage(bppvp[800]);
			}

			return false;
		});
	});
}(jQuery));