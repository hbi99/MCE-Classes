
(function(root, document, $) {
	'use strict';

	var mce_classes = {
		init: function() {
			// init all objects
			for (var n in this) {
				if (typeof(this[n].init) === 'function') {
					this[n].init();
				}
			}
			$('.mce_classes').on('click', '.nolink', this.do_event);
		},
		do_event: function(event) {
			var self = mce_classes,
				type = (typeof(event) === 'string')? event : event.type,
				target;

			switch (type) {
				// native events
				case 'click':
					target = event.target;
					var el = (target.nodeName.toLowerCase() === 'a')? $(target) : $(target).parents('a'),
						command = el.attr('href');

					event.preventDefault();
					self.do_event.call( el[0], command );
					break;
				// custom events
				case '/toggle-row/':
					var liEl = $(this).parents('li');
					if (liEl.hasClass('mce_expanded')) {
						liEl.removeClass('mce_expanded').css({'height': ''});
					} else {
						liEl.addClass('mce_expanded').css({'height': liEl[0].scrollHeight + 'px'});
					}
					break;
				case '/update-sort-order/':
					var rows = $('ul.sortable > li'),
						il = rows.length,
						i = 0;
					for (; i<il; i++) {
						$('input.field_font_order', rows[i])
							.val(i+1);
					}
					break;
				case '/add-font-family/':
					var newRow = $('ul.sortable').prepend('<li></li>').find('li:nth(0)');
					newRow.append( $('.row_template').clone().removeClass('row_template') );
					self.do_event('/update-sort-order/');
					break;
				case '/remove-font-family/':
					$(this).parents('li').remove();
					self.do_event('/update-sort-order/');
					break;
				case '/save-font-families/':
					var rnd = parseInt(Math.random() * 40, 10) + 15,
						row_data = [],
						rows = $('ul.sortable > li'),
						row,
						il = rows.length,
						i = 0;

					self.progress.set(rnd);

					for (; i<il; i++) {
						row = $(rows[i]);
						row_data.push({
							'order' : row.find('.field_font_order').val(),
							'name'  : row.find('.field_font_name').val(),
							'class' : row.find('.field_font_class').val(),
							'css'   : escape(row.find('.field_font_extended').val())
						});
					}

					$.ajax({
						type: 'POST',
						url: mcef_cfg.ajax_path,
						data: {
							nonce  : mcef_cfg.ajax_nonce,
							action : 'MCE_Classes/save_classes',
							data   : row_data
						},
						success: function(data) {
							//console.log(data);
							self.progress.set(100);
						}
					});
					break;
			}
		},
		sorter: {
			init: function() {
				mce_classes.do_event('/update-sort-order/');

				$('.sortable').sortable({
					start: this.do_event,
					stop: this.do_event,
					update: this.do_event
				});
				//.disableSelection();
			},
			do_event: function(event) {
				switch (event.type) {
					// custom events
					case 'sortstop':
						mce_classes.do_event('/update-sort-order/');
						break;
				}
			}
		},
		progress: {
			init: function() {
				this.el = $('.mce_classes .progress');
			},
			set: function(percentage, el) {
				var self = this,
					prEl = el || this.el;
				prEl.addClass('enabled')
					.css({'width': percentage +'%'});
				window.clearTimeout(this.timeout);
				if (percentage >= 100) {
					this.timeout = window.setTimeout(function() {
						prEl.css({'opacity': '0'});
						self.reset(prEl);
					}, 520);
				}
			},
			reset: function(prEl) {
				prEl = prEl || this.el;
				window.clearTimeout(this.timeout);
				window.setTimeout(function() {
					prEl.removeClass('enabled')
						.css({
							'width': '0',
							'opacity': '1'
						});
				}, 320);
			}
		}
	};
	root.mce_classes = mce_classes;

	$(document).ready(function() {
		mce_classes.init();
	});

})(window, document, jQuery);
