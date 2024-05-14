function send_email(){
    const email = $('#inputEmail').val().trim()
    const name = $('#inputName').val().trim()
    const request = $('#inputRequest').val().trim()
    if(email == "" || name == ""){
        alert("Please, type your name and your email.")
        return
    }

    const i = Math.round(Math.random() * 10)
    const j = Math.round(Math.random() * 10)
    const sum = i + j
    var userTest = prompt(`Small check: \nWhat is the sum of ${i} + ${j} ?`)
    if(userTest != sum){
        alert("Incorrect answer. Please try again.")
        return
    }
    $('#btnSend').prop("disabled", true)
    get_key({email, name, request})
    
}

function get_key(inputData){
    $.ajax({
        type: 'GET',
        success: function(key){
            sendEmailAfterCheck(key, inputData)
        },
        error: function(error){
            console.log(error)
            alert("An error occurred during the key check...")
            $('#btnSend').prop("disabled", false)
        },
        url: '/key.php'
    });
}

function sendEmailAfterCheck(key, inputData){
    inputData["key"] = key
    $.ajax({
        contentType: 'application/json',
        data: JSON.stringify(inputData),
        success: function(data){
            if(data == "OK"){
                $('#contactModal').modal('toggle')
                alert(`Your comment has be sent.
You will receive a message with your comment. 
If it's not the case, check your email address and try again.`)
            }else{
                alert("An error occurred during email sending...")
            }
            $('#btnSend').prop("disabled", false)
        },
        error: function(data){
            console.log(data)
            alert("An error occurred...")
            $('#btnSend').prop("disabled", false)
        },
        processData: false,
        type: 'POST',
        url: '/sendmail.php'
    });
}