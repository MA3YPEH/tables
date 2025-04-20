/**
 * JavaScript for the view.php.
 *
 * @module    mod_tables/updatecell
 * @copyright   2023 Mazur Egor <mazur.eh@edu.spbstu.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$(document).ready(function () {
    sendAllFocusOutToPHP()
});

$(document).click(function(event){
    //If click on other element set cell not focused
    if(event.target.classList.contains('m-tables-toolbar') || event.target.classList.contains('table-cell')){

        onFocusOutCell()

        // let cells = document.getElementById("prev_cell").value
        //
        // if(cells.includes(',')){
        //     cells = cells.split(',')
        //     for(let i = 0; i < cells.length; i++){
        //         sendFocusOutToPHP(cells[i])
        //     }
        // }
        // else{
        //     sendFocusOutToPHP(cells)
        // }
    }
    //If click on cell
    else if(event.target.name === 'cell_textarea'){
        if(document.getElementById('attach_cell_to_users').value === 'off' || document.getElementById('attach_cell_to_groups').value === 'off'){
            if(event.target.id !== document.getElementById("focused_cell").value){
                if(event.ctrlKey){
                    document.getElementById("focused_cell").value += "," + event.target.id;
                    document.getElementById("prev_cell").value += "," + event.target.id;

                    document.getElementById("focused_cell_content").value = event.target.value;
                    document.getElementById("font-family-selector").value = event.target.style.fontFamily;
                    document.getElementById("font-size-selector").value = event.target.style.fontSize.replace("pt", "");

                    setFocusedVisibility(event.target)
                    setFocusedFont(event.target)
                    setFocusedAlign(event.target)

                    onClickCellFocus(event.target)
                }
                else if(event.shiftKey){

                }
                else{
                    onFocusOutCell();

                    document.getElementById("focused_cell").value = event.target.id;
                    document.getElementById("prev_cell").value = event.target.id;
                    document.getElementById("focused_cell_content").value = event.target.value;
                    document.getElementById("font-family-selector").value = event.target.style.fontFamily;
                    document.getElementById("font-size-selector").value = event.target.style.fontSize.replace("pt", "");

                    setFocusedVisibility(event.target)
                    setFocusedFont(event.target)
                    setFocusedAlign(event.target)

                    onClickCellFocus(event.target)
                }
            }
        }
        else{
            onFocusInCellAttach(event.target)
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