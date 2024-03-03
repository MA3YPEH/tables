let conn = new WebSocket("ws://localhost:8081");
$(document).ready(function() {
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
        let data = JSON.parse(e.data);
        let style = "";

        switch(data.update_type){
            case "input":
                let cell = document.getElementById(data.cell_id);
                cell.value = data.cell_content;

                if (!data.cell_focus) {
                    cell.removeAttribute("disabled");
                    cell.setAttribute("class", "resizable");
                } else if (data.cell_focus) {
                    cell.setAttribute("disabled", "");
                    cell.removeAttribute("class", "resizable");
                }
                break;
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
        }
    };
});