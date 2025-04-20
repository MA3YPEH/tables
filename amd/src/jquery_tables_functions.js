/**
 * JavaScript for the view.php.
 *
 * @module    mod_tables/updatecell
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$(document).ready(function () {
    let data = {
        update_type: "focusout",
        cell_id: document.getElementById("prev_element").value
    };

    if(localStorage.socket !== "false") {
        socket.emit('send', {
            room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
            message: data
        });
    }
});

$(document).click(function(e){
    console.log($(event.target).attr('class'))
    if($(event.target).attr('class') === 'm-tables-toolbar' || $(event.target).attr('class') === 'table-cell'){
        let main = document.getElementById('main_table');

        onFocusOutCell(document.getElementById("focused_cell").value)

        let data = {
            update_type: "focusout",
            table_id: main.getAttribute('data-moduleinstance'),
            sheet_id: main.getAttribute('data-sheet'),
            cell_id: document.getElementById("prev_element").value
        };

        $.ajax({
            method: "POST",
            url: "update_cell_focus.php",
            data: data
        });

        if(localStorage.socket !== "false") {
            socket.emit('send', {
                room: document.getElementById('main_table').getAttribute('data-moduleinstance'),
                message: data
            });
        }
    }
});

// Trigger action when the contexmenu is about to be shown
$('.m-tables-sheet-select').bind("contextmenu", function (event) {
    let active_sheet = document.getElementById('main_table').getAttribute('data-sheet');

    if(active_sheet !== event.target.id.replace('sheet_', '')){
        $("#delete_sheet").attr('id', 'delete_'.concat(event.target.id))
        // Avoid the real one
        event.preventDefault();
        // Show contextmenu
        $(".m-sheet-custom-menu").finish().toggle(100).
            // In the right position (the mouse)
            css({
                top: event.pageY + "px",
                left: event.pageX + "px"
            });
    }
});


// If the document is clicked somewhere
$(document).bind("mousedown", function (e) {

    // If the clicked element is not the menu
    if (!$(e.target).parents(".m-sheet-custom-menu").length > 0) {

        // Hide it
        $(".m-sheet-custom-menu").hide(100);
    }
});


// If the menu element is clicked
$(".m-sheet-custom-menu li").click(function(){
    let custom_menu = $(".m-sheet-custom-menu");
    let sheet_id = $(this).attr("id").replace("delete_sheet_", '')

    // This is the triggered action name
    switch($(this).attr("data-action")) {
        // A case for each action. Your actions here
        case "delete_sheet":{
            let data = {
                update_type: "delete_sheet",
                table_id: custom_menu.attr("id").replace("custom_menu_", ""),
                sheet_id: sheet_id
            };

            //Send information to create_sheet.php for updating database
            $.ajax({
                method: "POST",
                url: "create_sheet.php",
                data: data
            });
            break;
        }
    }

    $(this).attr("id", "delete_sheet")
    $("#sheet_".concat(sheet_id)).remove()
    // Hide it AFTER the action was triggered
    custom_menu.hide(100);
});