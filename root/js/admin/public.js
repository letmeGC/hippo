function del(id, table) {
    var gnl = confirm("确定删除?");
    if (gnl) {
        $.post("/Admin/delete", { 'id': id, 'table': table },
            function(data) {
                alert(data.msg);
                if (data.code) {
                    window.location.reload();
                }
            },
            "json");
    }
}

function search(str, tagid) {
    var x = document.getElementById(tagid);
    window.location.href = "/Admin/" + str + "&search_category=" + x.value;
}