"use strict";

function queryParams(p){
    return {
      "task_id": $('#comment_task_id').val(),
      limit:p.limit,
      sort:p.sort,
      order:p.order,
      offset:p.offset,
      search:p.search
    };
}

$(document).on('change','#project_id',function(){
	$.ajax({
        type: "POST",
        url: base_url+'projects/get_project_users/'+$(this).val(), 
        dataType: "json",
        success: function(result) 
        {	
        	var user = '';
			$.each(result, function (key, val) {
				user +=' <option value="'+val.id+'">'+val.full_name+'</option>';
			});
			$("#users_append").html(user);
        }        
    });
});

!function (a) {
    var t = function () {
        this.$body = a("body")
    };
    t.prototype.init = function () {
        a('[data-plugin="dragula"]').each(function () {
            var t = a(this).data("containers"), n = []; 
            if (t) {
                if (isAdmin) {
                    for (var i = 0; i < t.length; i++) 
                        n.push(a("#" + t[i])[0]); 
                } else {
                    for (var i = 0; i < t.length - 1; i++) 
                        n.push(a("#" + t[i])[0]); 
                }
            }
            else n = [a(this)[0]];
            var r = a(this).data("handleclass");
            r ? dragula(n, {
                moves: function (a, t, n) {
                    return n.classList.contains(r)
                }
            }) : dragula(n).on('drop', function (el, target, source, sibling) {

                var sort = [];
                $("#"+target.id+" > div").each(function () {
                    sort[$(this).index()]=$(this).attr('id');
                });

                var id = $(el).data('id');
                var new_status = $("#"+target.id).data('status');

                $("#"+source.id).parent().find('.count').text($("#"+source.id+" > div").length);
                $("#"+target.id).parent().find('.count').text($("#"+target.id+" > div").length);
                 
                if(new_status == 4){
                    $(el).find('.task_date_div').addClass('d-none');
                    $(el).find('.task_completed_div').removeClass('d-none');
                    $(el).find('.task_review_div').addClass('d-none');
                }if(new_status == 3){
                    $(el).find('.task_date_div').addClass('d-none');
                    $(el).find('.task_completed_div').addClass('d-none');
                    $(el).find('.task_review_div').removeClass('d-none');
                }else{
                    $(el).find('.task_date_div').removeClass('d-none');
                    $(el).find('.task_completed_div').addClass('d-none');
                    $(el).find('.task_review_div').addClass('d-none');
                }

                $.ajax({
                    url: base_url+'projects/task_status_update',
                    type: 'POST',
                    data: "id="+id+"&status="+new_status,
                    dataType: "json",
                    success: function(){ 
                    }
                });
                
            });
        })
    }, a.Dragula = new t, a.Dragula.Constructor = t
}(window.jQuery), function (a) {
    a.Dragula.init()
}(window.jQuery);