/**
 * Change update in sheet table
 *
 * @param {object} object html object.
 */
function onchangeUpdate(object){
    let data = {
        sheet_id: object.id,
        update: object.checked
    };

    console.log(data)

    $.ajax({
        method: "POST",
        url: "update_upload.php",
        data: data
    });
}