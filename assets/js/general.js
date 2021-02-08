
// Редактирование
function onContextMenu(e,row) {
    e.preventDefault();
    $(this).treegrid('select', row.id);
    $('#mm').menu('show',{
            left: e.pageX,
            top: e.pageY
    });
}

function hide_notifications() {
    // Show notification
    $('.success').fadeOut(50);

    // Show notification
    $('.error').fadeOut(50);

    // Show notification
    $('.info').fadeOut(50);
}

// Свернуть узел
function collapse() {
    var t = $('#tg');
    var node = t.treegrid('getSelected');
    if (node) {
            t.treegrid('collapse', node.id);
    }
}

// Развернуть узел
function expand() {
    var t = $('#tg');
    var node = t.treegrid('getSelected');
    if (node) {
            t.treegrid('expand', node.id);
    }
}