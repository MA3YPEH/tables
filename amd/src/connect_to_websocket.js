let conn = new WebSocket("ws://localhost:8081");
$(document).ready(function() {
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
        let data = JSON.parse(e.data);
        let cell = document.getElementById(data.cell_id);
        cell.value = data.cell_content;

        let style = "";
        style = style.concat("height:", data.cell_height, "px; width:", data.cell_width, "px");

        cell.setAttribute("style", style);
        if (!data.cell_focus) {
            cell.removeAttribute("disabled");
            cell.setAttribute("class", "resizable");
        } else if (data.cell_focus) {
            cell.setAttribute("disabled", "");
            cell.removeAttribute("class", "resizable");
        }
    };
});