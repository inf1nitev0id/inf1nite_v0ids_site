class Forum {
		constructor(elem) {
			this._elem = elem;
			elem.onclick = this.onClick.bind(this); // (*)
		}

		reply(id) {
			let form = $('#write_comment');
			form.parent().attr('hidden', true);
			form.detach().appendTo('#reply' + id);
			form.parent().removeAttr('hidden');
			$('#reply_id').attr('value', id);
			$('#comment_area').focus();
		}

		close(id) {
			$('#write_comment').parent().attr('hidden', true);
		}

		delete(id) {
			$('#delete_text').html($('#comment_text' + id).html());
			$('#delete_id').attr('value', id);
		}

		like(id) {
			alert('like  ' + id);
		}

		dislike(id) {
			alert('dislike ' + id);
		}

		onClick(event) {
			let action = event.target.dataset.action;
			if (action) {
				let id = event.target.closest("[data-id]").dataset.id;
				this[action](id);
			}
		};
}
