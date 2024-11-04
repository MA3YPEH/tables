

let socket = io('wsserver.na4u.ru:80')
let username = document.getElementById('main_table').getAttribute('data-user');
let room = document.getElementById('main_table').getAttribute('data-moduleinstance');
let isStaff = "false";

$(document).ready(function() {
    socket.emit('login', {username: username, isStaff: isStaff});
    socket.emit('subscribe', {room: room});

    // $(window ).unload(function() {
    //     socket.emit('unsubscribe', {room: room});
    // });
    $( window ).on( "unload", function() {
        socket.emit('unsubscribe', {room: room});
    } );

    socket.on('message', function(message){
        if(message.sender !== document.getElementById('main_table').getAttribute('data-user')){
            let style = "";
            let cell;
            let data = message.message;
            //console.log(data)

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
                    let attached_cell = cell.getAttribute('data-attached');

                    if(attached_cell === '1' || document.getElementById('main_table').getAttribute('data-user-role') === 'teacher'){
                        try{
                            cell.removeAttribute("disabled");
                            cell.setAttribute("class", "resizable");
                        }
                        catch (e){
                            console.log("No focused cell")
                            console.log(e)
                        }
                        break;
                    }
                    break;
                case "attach_cells":
                    if(document.getElementById('main_table').getAttribute('data-user') === data.user_id){
                        location.reload();
                    }
                    break;
                case "delete_cells":
                    if(document.getElementById('main_table').getAttribute('data-user') === data.user_id){
                        location.reload();
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
        }
    });
});


// $(document).ready(function() {
//     conn.onopen = function(e) {
//         console.log("Connection established!");
//     };
//
//     conn.onmessage = function(e) {
//         console.log(e.data);
//         let data = JSON.parse(e.data);
//         let style = "";
//         let cell;
//
//         switch(data.update_type){
//             case "input":
//                 cell = document.getElementById(data.cell_id);
//                 cell.value = data.cell_content;
//                 break;
//             case "focusin":
//                 cell = document.getElementById(data.cell_id);
//
//                 cell.setAttribute("disabled", "");
//                 cell.removeAttribute("class", "resizable");
//                 break;
//             case "focusout":
//                 cell = document.getElementById(data.cell_id);
//                 let attached_cell = cell.getAttribute('data-attached');
//
//                 if(attached_cell === '1' || document.getElementById('main_table').getAttribute('data-user-role') === 'teacher'){
//                     try{
//                         cell.removeAttribute("disabled");
//                         cell.setAttribute("class", "resizable");
//                     }
//                     catch (e){
//                         console.log("No focused cell")
//                         console.log(e)
//                     }
//                     break;
//                 }
//                 break;
//             case "attach_cells":
//                 if(document.getElementById('main_table').getAttribute('data-user') === data.user_id){
//                     location.reload();
//                 }
//                 break;
//             case "delete_cells":
//                 if(document.getElementById('main_table').getAttribute('data-user') === data.user_id){
//                     location.reload();
//                 }
//                 break;
//             case "resize_h":
//                 let row = document.getElementById(data.name);
//                 style = style.concat("height:", data.height, "px;");
//                 row.setAttribute("style", style);
//                 break;
//             case "resize_w":
//                 let column = document.getElementById(data.name);
//                 style = style.concat("width:", data.width, "px;");
//
//                 column.setAttribute("style", style);
//                 break;
//             case "font":
//                 switch(data.button_type){
//                     case "font-family-selector":
//                         document.getElementById(data['cell_id']).style.fontFamily = data.input_content;
//                         break;
//                     case "font-size-selector":
//                         document.getElementById(data['cell_id']).style.fontSize = data.input_content.concat("pt");
//                         break;
//                     case "font-bold-button":
//                         document.getElementById(data['cell_id']).style.fontWeight = data.input_content;
//                         break;
//                     case "font-italic-button":
//                         document.getElementById(data['cell_id']).style.fontStyle = data.input_content;
//                         break;
//                     case "font-underline-button":
//                         document.getElementById(data['cell_id']).style.textDecoration = data.input_content;
//                         break;
//                     case "text-left-button":
//                         document.getElementById(data['cell_id']).style.textAlign = data.input_content;
//                         break;
//                     case "text-center-button":
//                         document.getElementById(data['cell_id']).style.textAlign = data.input_content;
//                         break;
//                     case "text-right-button":
//                         document.getElementById(data['cell_id']).style.textAlign = data.input_content;
//                         break;
//                 }
//                 break;
//         }
//     };
// });