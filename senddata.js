function update_cell(object, conn){
    let id_field = object.id;
    let value_field = object.value;
    let table_id = object.name.replace("cell_module_", "");
    let height = object.offsetHeight;
    let width = object.offsetWidth;
    let data = {table_id: table_id, cell_id: id_field, cell_content: value_field, cell_height: height, cell_width: width};


    conn.send(JSON.stringify(data));

    $.ajax({
        method: "POST",
        url: "updatecell.php",
        data: data
    });

}