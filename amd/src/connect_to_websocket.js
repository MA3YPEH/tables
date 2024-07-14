let conn = new WebSocket("ws://localhost:8081");
$(document).ready(function() {
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
        let data = JSON.parse(e.data);
        let style = "";
        let cell;

        switch(data.update_type){
            case "input":
                cell = document.getElementById(data.cell_id);
                cell.value = data.cell_content;
                break;
            case "focusin":
                cell = document.getElementById(data.cell_id);

                cell.setAttribute("disabled", "");
                cell.removeAttribute("class", "resizable");
                break;
            case "focusout":
                cell = document.getElementById(data.cell_id);
                let attached_cells = document.getElementById(['attached_cells']).value;

                if(attached_cells == null){
                    cell.removeAttribute("disabled");
                    cell.setAttribute("class", "resizable");
                    break;
                }
                else{
                    try{
                        if(isAttachedCell(attached_cells, cell.id)){
                            cell.removeAttribute("disabled");
                            cell.setAttribute("class", "resizable");
                        }
                    }
                    catch (e){
                        console.log("No focused cell")
                        console.log(e)
                    }
                    break;
                }
            case "resize_h":
                let row = document.getElementById(data.name);
                style = style.concat("height:", data.height, "px;");
                row.setAttribute("style", style);
                break;
            case "resize_w":
                let column = document.getElementById(data.name);
                style = style.concat("width:", data.width, "px;");

                column.setAttribute("style", style);
                break;
            case "font":
                switch(data.button_type){
                    case "font-family-selector":
                        document.getElementById(data['cell_id']).style.fontFamily = data.input_content;
                        break;
                    case "font-size-selector":
                        document.getElementById(data['cell_id']).style.fontSize = data.input_content.concat("pt");
                        break;
                    case "font-bold-button":
                        document.getElementById(data['cell_id']).style.fontWeight = data.input_content;
                        break;
                    case "font-italic-button":
                        document.getElementById(data['cell_id']).style.fontStyle = data.input_content;
                        break;
                    case "font-underline-button":
                        document.getElementById(data['cell_id']).style.textDecoration = data.input_content;
                        break;
                    case "text-left-button":
                        document.getElementById(data['cell_id']).style.textAlign = data.input_content;
                        break;
                    case "text-center-button":
                        document.getElementById(data['cell_id']).style.textAlign = data.input_content;
                        break;
                    case "text-right-button":
                        document.getElementById(data['cell_id']).style.textAlign = data.input_content;
                        break;
                }
                break;
        }
    };
});