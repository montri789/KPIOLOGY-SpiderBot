$(function(){
	
	jQuery.fn.center = function () {
		this.css("position","absolute");
		this.css("top",(($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
		this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
		return this;
	}
	
	$(window).resize(function(e){
		this.css("top",(($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
		this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
	});

	
	jQuery.fn.loading = function(options){
		if(options.status == 'show'){
			this.append('<div class="loading"></div>');
			$('.loading').center();
		}else if(options.status == 'hide'){
			$('.loading').remove();
		}
        }
});